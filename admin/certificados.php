<?php
/**
 * admin/certificados.php — Emissão, importação (CSV), revogação e download dos certificados.
 */
declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
require_once dirname(__DIR__) . '/includes/certificates.php';
require_role('superadmin', 'admin');

$db = getDB();

// ─── Download do PDF ──────────────────────────────────────
if (isset($_GET['pdf'])) {
    $stmt = $db->prepare('SELECT * FROM certificates WHERE id = ?');
    $stmt->execute([(int)$_GET['pdf']]);
    $row = $stmt->fetch();
    if (!$row) { http_response_code(404); exit('Certificado nao encontrado.'); }
    require_once dirname(__DIR__) . '/includes/certificate_pdf.php';
    $pdf = cert_pdf($row);
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="certificado-' . $row['code'] . '.pdf"');
    header('Content-Length: ' . strlen($pdf));
    echo $pdf;
    exit;
}

$msg = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'issue') {
        [$data, $err] = cert_validate($_POST);
        if ($err) {
            $errors[] = $err;
        } else {
            $row = cert_insert($db, $data, $adminId);
            $msg = 'Certificado emitido: ' . $row['code'];
        }
    }

    if ($action === 'import') {
        $file = $_FILES['csv'] ?? null;
        if (!$file || $file['error'] !== UPLOAD_ERR_OK || $file['size'] > 1024 * 1024) {
            $errors[] = 'Envie um CSV de até 1 MB.';
        } else {
            $fh = fopen($file['tmp_name'], 'r');
            $first = (string)fgets($fh);
            rewind($fh);
            $sep = substr_count($first, ';') > substr_count($first, ',') ? ';' : ',';
            $header = array_map(static fn($h) => strtolower(trim(preg_replace('/^\xEF\xBB\xBF/', '', (string)$h))), fgetcsv($fh, 0, $sep, '"', '') ?: []);
            $valid = [];
            $line = 1;
            if (!in_array('nome', $header, true) || !in_array('evento', $header, true)) {
                $errors[] = 'O CSV precisa ter ao menos as colunas: nome, evento (opcionais: tipo, horas, data, email).';
            }
            while (!$errors && ($cols = fgetcsv($fh, 0, $sep, '"', '')) !== false) {
                $line++;
                if ($cols === [null] || !array_filter($cols, static fn($v) => trim((string)$v) !== '')) continue;
                if (count($valid) >= 500) { $errors[] = 'Limite de 500 linhas por importação.'; break; }
                $assoc = array_combine($header, array_pad(array_slice($cols, 0, count($header)), count($header), ''));
                [$data, $err] = cert_validate($assoc);
                if ($err) { $errors[] = "Linha {$line}: {$err}"; if (count($errors) >= 10) break; }
                else { $valid[] = $data; }
            }
            fclose($fh);
            // Tudo ou nada: se houver qualquer erro, nada é emitido.
            if (!$errors && $valid) {
                $db->beginTransaction();
                try {
                    foreach ($valid as $d) { cert_insert($db, $d, $adminId); }
                    $db->commit();
                    $msg = count($valid) . ' certificados emitidos.';
                } catch (Throwable $e) {
                    $db->rollBack();
                    error_log('[CoBraLT] import certificados: ' . $e->getMessage());
                    $errors[] = 'Erro ao importar. Nada foi emitido.';
                }
            } elseif (!$errors) {
                $errors[] = 'Nenhuma linha encontrada no CSV.';
            }
        }
    }

    if ($action === 'revoke' || $action === 'reactivate') {
        cert_set_status($db, (int)($_POST['id'] ?? 0), $action === 'revoke', trim((string)($_POST['reason'] ?? '')));
        $msg = $action === 'revoke' ? 'Certificado revogado.' : 'Certificado reativado.';
    }
}

$q = trim((string)($_GET['q'] ?? ''));
$params = [];
$where = '';
if ($q !== '') {
    $like = '%' . $q . '%';
    $where = ' WHERE holder_name LIKE ? OR code LIKE ? OR event_name LIKE ?';
    $params = [$like, $like, $like];
}
$stmt = $db->prepare('SELECT * FROM certificates' . $where . ' ORDER BY id DESC LIMIT 200');
$stmt->execute($params);
$rows = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Certificados — Admin CoBraLT</title>
  <style>
    body{font-family:'Segoe UI',sans-serif;background:#F1F5F9;color:#1E293B;margin:0;padding:2rem;}
    h1{color:#002B4E;font-size:1.3rem;} h2{color:#002B4E;font-size:1rem;margin:0 0 .75rem;} a{color:#0284C7;}
    .card{background:#fff;border:1px solid #E2E8F0;border-radius:12px;padding:1.25rem;margin-bottom:1.25rem;}
    .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:.75rem;}
    label{display:block;font-size:.75rem;font-weight:600;color:#475569;text-transform:uppercase;}
    input,select{width:100%;box-sizing:border-box;padding:.55rem .7rem;border:1.5px solid #E2E8F0;border-radius:8px;font:inherit;}
    button{padding:.55rem 1rem;border:0;border-radius:8px;background:#002B4E;color:#fff;font:inherit;font-weight:600;cursor:pointer;}
    button.danger{background:#EF4444;} button.alt{background:#0284C7;}
    .ok{background:#D1FAE5;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;} .err{background:#FEE2E2;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;}
    table{width:100%;border-collapse:collapse;font-size:.85rem;} th,td{text-align:left;padding:.5rem;border-bottom:1px solid #E2E8F0;vertical-align:top;}
    .badge{padding:.1rem .5rem;border-radius:99px;font-size:.75rem;} .valido{background:#D1FAE5;} .revogado{background:#FEE2E2;}
    form.inline{display:flex;gap:.4rem;} .wrap{overflow-x:auto;}
  </style>
</head>
<body>
  <p><a href="dashboard.php">← Painel</a> · <a href="autenticar.php">Autenticar certificado</a></p>
  <h1>Certificados</h1>

  <?php if ($msg): ?><div class="ok"><?= e($msg) ?></div><?php endif; ?>
  <?php foreach ($errors as $er): ?><div class="err"><?= e($er) ?></div><?php endforeach; ?>

  <div class="card">
    <h2>Emitir certificado</h2>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="action" value="issue">
      <div class="grid">
        <div><label>Nome *<input name="nome" required maxlength="180"></label></div>
        <div><label>Evento *<input name="evento" required maxlength="220"></label></div>
        <div><label>Tipo<select name="tipo"><?php foreach (cert_types() as $k => $v): ?><option value="<?= e($k) ?>"><?= e($v) ?></option><?php endforeach; ?></select></label></div>
        <div><label>Horas<input name="horas" type="number" min="1" max="9999"></label></div>
        <div><label>Data<input name="data" type="date" value="<?= date('Y-m-d') ?>"></label></div>
        <div><label>E-mail (opcional)<input name="email" type="email" maxlength="190"></label></div>
      </div>
      <p><button type="submit">Emitir</button></p>
    </form>
  </div>

  <div class="card">
    <h2>Importar CSV</h2>
    <p>Colunas: <code>nome</code>, <code>evento</code> (obrigatórias); <code>tipo</code>, <code>horas</code>, <code>data</code>, <code>email</code> (opcionais). Separador vírgula ou ponto e vírgula. Máx. 500 linhas. Se qualquer linha tiver erro, nada é emitido.</p>
    <form method="post" enctype="multipart/form-data" class="inline">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="action" value="import">
      <input type="file" name="csv" accept=".csv,text/csv" required>
      <button type="submit" class="alt">Importar</button>
    </form>
  </div>

  <div class="card">
    <h2>Emitidos (últimos 200)</h2>
    <form method="get" class="inline" style="margin-bottom:1rem;">
      <input name="q" value="<?= e($q) ?>" placeholder="Buscar por nome, código ou evento">
      <button type="submit">Buscar</button>
    </form>
    <div class="wrap">
    <table>
      <thead><tr><th>Código</th><th>Nome</th><th>Evento</th><th>Tipo</th><th>Data</th><th>Status</th><th>Ações</th></tr></thead>
      <tbody>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><a href="<?= e(cert_verify_url($r['code'])) ?>" target="_blank" rel="noopener"><?= e($r['code']) ?></a></td>
          <td><?= e($r['holder_name']) ?></td>
          <td><?= e($r['event_name']) ?></td>
          <td><?= e(cert_types()[$r['cert_type']] ?? $r['cert_type']) ?></td>
          <td><?= e($r['issued_at']) ?></td>
          <td><span class="badge <?= e($r['status']) ?>"><?= e($r['status']) ?></span><?= $r['revoked_reason'] ? '<br><small>' . e($r['revoked_reason']) . '</small>' : '' ?></td>
          <td>
            <a href="?pdf=<?= (int)$r['id'] ?>">PDF</a>
            <form method="post" class="inline" style="margin-top:.4rem;">
              <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <?php if ($r['status'] === 'valido'): ?>
                <input type="hidden" name="action" value="revoke">
                <input name="reason" placeholder="Motivo" maxlength="255" required>
                <button class="danger" onclick="return confirm('Revogar este certificado?')">Revogar</button>
              <?php else: ?>
                <input type="hidden" name="action" value="reactivate">
                <button class="alt">Reativar</button>
              <?php endif; ?>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?><tr><td colspan="7">Nenhum certificado.</td></tr><?php endif; ?>
      </tbody>
    </table>
    </div>
  </div>
</body>
</html>
