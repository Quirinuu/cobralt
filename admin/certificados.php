<?php
/**
 * admin/certificados.php — Emissão, importação (CSV), revogação e download dos certificados.
 */
declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
require_once dirname(__DIR__) . '/includes/certificate_issue.php';
require_role('superadmin', 'admin');

$db = getDB();
cert_ensure_schema($db);

// ─── Download do PDF ──────────────────────────────────────
if (isset($_GET['pdf'])) {
    $stmt = $db->prepare('SELECT * FROM certificates WHERE id = ?');
    $stmt->execute([(int)$_GET['pdf']]);
    $row = $stmt->fetch();
    if (!$row) { http_response_code(404); exit('Certificado nao encontrado.'); }
    $pdf = cert_get_pdf($db, $row);
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="certificado-' . $row['code'] . '.pdf"');
    header('Content-Length: ' . strlen($pdf));
    echo $pdf;
    exit;
}

$msg = '';
$errors = [];
$templates = $db->query('SELECT id, name FROM certificate_templates ORDER BY name')->fetchAll();
$templateId = (int)($_POST['template_id'] ?? 0);
if ($templateId && !cert_load_template($db, $templateId)) { $templateId = 0; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'issue') {
        [$data, $err] = cert_validate($_POST);
        if ($err) {
            $errors[] = $err;
        } else {
            $row = cert_issue_stored($db, $data, $adminId, $templateId);
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
                    foreach ($valid as $d) { cert_issue_stored($db, $d, $adminId, $templateId); }
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
admin_page_start('Certificados', 'certificados', 'Emitir, importar em lote, baixar e revogar');
admin_alerts($msg, $errors);
?>
<div class="grid-2">
  <section class="card">
    <div class="card-header"><h3>Emitir um certificado</h3></div>
    <div class="card-body">
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="issue">
        <div class="grid-form">
          <div class="field"><label>Modelo</label>
            <select name="template_id"><option value="0">Layout padrão</option><?php foreach ($templates as $t): ?><option value="<?= (int)$t['id'] ?>"><?= e($t['name']) ?></option><?php endforeach; ?></select></div>
          <div class="field"><label>Tipo</label>
            <select name="tipo"><?php foreach (cert_types() as $k => $v): ?><option value="<?= e($k) ?>"><?= e($v) ?></option><?php endforeach; ?></select></div>
          <div class="field"><label>Nome da pessoa *</label><input name="nome" required maxlength="180"></div>
          <div class="field"><label>Evento *</label><input name="evento" required maxlength="220"></div>
          <div class="field"><label>Carga horária</label><input name="horas" type="number" min="1" max="9999" placeholder="horas"></div>
          <div class="field"><label>Data</label><input name="data" type="date" value="<?= date('Y-m-d') ?>"></div>
          <div class="field"><label>E-mail (opcional)</label><input name="email" type="email" maxlength="190"></div>
        </div>
        <div class="btn-row"><button type="submit" class="btn">Emitir certificado</button></div>
      </form>
    </div>
  </section>

  <section class="card">
    <div class="card-header"><h3>Importar vários (CSV)</h3><a class="btn-sm outline" href="../assets/admin/exemplo-certificados.csv" download>Baixar CSV de exemplo</a></div>
    <div class="card-body">
      <p class="hint" style="margin-bottom:1rem">Uma linha por pessoa. Obrigatórias: <strong>nome</strong> e <strong>evento</strong>. Opcionais: tipo, horas, data, email. Máx. 500 linhas — se uma linha tiver erro, nada é emitido.</p>
      <button type="button" class="csv-example" id="csvExample" aria-label="Ampliar exemplo de planilha">
        <img src="../assets/admin/exemplo-csv-certificados.png" alt="Exemplo de planilha com as colunas nome, evento, tipo, horas, data e email">
        <span>🔍 Clique para ampliar</span>
      </button>
      <form method="post" enctype="multipart/form-data" style="margin-top:1rem">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="import">
        <div class="grid-form">
          <div class="field"><label>Modelo</label>
            <select name="template_id"><option value="0">Layout padrão</option><?php foreach ($templates as $t): ?><option value="<?= (int)$t['id'] ?>"><?= e($t['name']) ?></option><?php endforeach; ?></select></div>
          <div class="field"><label>Arquivo CSV</label><input type="file" name="csv" accept=".csv,text/csv" required></div>
        </div>
        <div class="btn-row"><button type="submit" class="btn">Importar e emitir</button></div>
      </form>
    </div>
  </section>
</div>

<section class="card">
  <div class="card-header">
    <h3>Emitidos (últimos 200)</h3>
    <form method="get" class="inline-form"><input type="search" name="q" value="<?= e($q) ?>" placeholder="Buscar por nome, código ou evento"><button class="btn-sm" type="submit">Buscar</button></form>
  </div>
  <div class="table-wrap">
  <table>
    <thead><tr><th>Código</th><th>Nome</th><th>Evento</th><th>Tipo</th><th>Data</th><th>Status</th><th>Ações</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td class="mono"><a href="autenticar.php?c=<?= e(rawurlencode($r['code'])) ?>"><?= e($r['code']) ?></a></td>
        <td><strong><?= e($r['holder_name']) ?></strong></td>
        <td><?= e($r['event_name']) ?></td>
        <td><?= e(cert_types()[$r['cert_type']] ?? $r['cert_type']) ?></td>
        <td><?= e(date('d/m/Y', (int)strtotime((string)$r['issued_at']))) ?></td>
        <td><span class="badge <?= e($r['status']) ?>"><?= e($r['status']) ?></span><?= $r['revoked_reason'] ? '<br><small class="hint">' . e($r['revoked_reason']) . '</small>' : '' ?></td>
        <td>
          <div class="actions">
            <a class="btn-sm outline" href="?pdf=<?= (int)$r['id'] ?>">PDF</a>
            <a class="btn-sm outline" href="<?= e(cert_verify_url($r['code'])) ?>" target="_blank" rel="noopener">Link</a>
            <form method="post" class="inline-form" style="flex-wrap:nowrap">
              <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <?php if ($r['status'] === 'valido'): ?>
                <input type="hidden" name="action" value="revoke">
                <input type="text" name="reason" placeholder="Motivo" maxlength="255" required style="min-width:110px">
                <button class="btn-sm danger" onclick="return confirm('Revogar este certificado?')">Revogar</button>
              <?php else: ?>
                <input type="hidden" name="action" value="reactivate">
                <button class="btn-sm">Reativar</button>
              <?php endif; ?>
            </form>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$rows): ?><tr><td colspan="7" class="empty">Nenhum certificado emitido ainda.</td></tr><?php endif; ?>
    </tbody>
  </table>
  </div>
</section>

<div class="lightbox" id="lightbox" hidden><img src="../assets/admin/exemplo-csv-certificados.png" alt="Exemplo ampliado da planilha"><button type="button" class="btn" id="lightboxClose">Fechar</button></div>
<style>
  .csv-example{display:block;width:100%;padding:0;border:1.5px solid var(--slate-200);border-radius:10px;background:#fff;cursor:zoom-in;position:relative;overflow:hidden;}
  .csv-example img{display:block;width:100%;height:auto;}
  .csv-example span{position:absolute;right:8px;bottom:8px;background:rgba(0,43,78,.88);color:#fff;font-size:.75rem;font-weight:600;padding:.3rem .6rem;border-radius:6px;}
  .csv-example:hover{border-color:var(--sky);}
  .lightbox{position:fixed;inset:0;background:rgba(15,23,42,.8);z-index:500;display:flex;flex-direction:column;gap:1rem;align-items:center;justify-content:center;padding:2rem;}
  .lightbox[hidden]{display:none;} .lightbox img{max-width:min(1100px,100%);max-height:80vh;background:#fff;border-radius:10px;}
</style>
<?php
admin_page_end('<script>
(function(){var lb=document.getElementById("lightbox");
document.getElementById("csvExample").onclick=function(){lb.hidden=false;};
lb.onclick=function(e){if(e.target===lb||e.target.id==="lightboxClose")lb.hidden=true;};
document.addEventListener("keydown",function(e){if(e.key==="Escape")lb.hidden=true;});})();
</script>');
