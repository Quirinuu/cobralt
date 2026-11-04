<?php
/**
 * admin/certificados.php — Emissão, importação (CSV), revogação e download dos certificados.
 */
declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
require_once dirname(__DIR__) . '/includes/certificate_issue.php';
require_once dirname(__DIR__) . '/includes/certificate_import.php';
require_role('superadmin', 'admin');

$db = getDB();
cert_ensure_schema($db);
cert_seed_default_templates($db);

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

// ─── ZIP com os PDFs de vários certificados ───────────────
if (isset($_GET['zip'])) {
    $ids = array_slice(array_values(array_filter(array_map('intval', explode(',', (string)$_GET['zip'])))), 0, CERT_IMPORT_MAX_ROWS);
    if (!$ids || !class_exists('ZipArchive')) { http_response_code(400); exit('Nada para baixar.'); }
    $in = implode(',', array_fill(0, count($ids), '?'));
    $st = $db->prepare("SELECT * FROM certificates WHERE id IN ($in) ORDER BY holder_name");
    $st->execute($ids);
    $tmp = tempnam(sys_get_temp_dir(), 'certzip');
    $zip = new ZipArchive();
    $zip->open($tmp, ZipArchive::OVERWRITE);
    $used = [];
    foreach ($st->fetchAll() as $c) {
        $base = preg_replace('/[^A-Za-z0-9._ -]+/', '', (string)iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', (string)$c['holder_name'])) ?: 'certificado';
        $name = trim($base) . ' - ' . $c['code'] . '.pdf';
        $zip->addFromString($name, cert_get_pdf($db, $c));
    }
    $zip->close();
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="certificados.zip"');
    header('Content-Length: ' . filesize($tmp));
    readfile($tmp);
    @unlink($tmp);
    exit;
}

$msg = '';
$errors = [];
$importedIds = [];
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
            cert_log_action($db, 'emissao', 'Certificado avulso: ' . $data['holder_name'] . ' — ' . $data['event_name'], [(int)$row['id']], $adminId);
            $msg = 'Certificado emitido: ' . $row['code'];
        }
    }

    if ($action === 'import') {
        [$rows, $err] = cert_table_from_upload($_FILES['csv'] ?? []);
        $valid = [];
        if ($err) {
            $errors[] = $err;
        } else {
            foreach ($rows as $r) {
                [$data, $verr] = cert_validate(cert_normalize_row($r));
                if ($verr) { $errors[] = 'Linha ' . $r['_linha'] . ': ' . $verr; if (count($errors) >= 10) break; }
                else { $valid[] = $data; }
            }
        }
        // Tudo ou nada: se houver qualquer erro, nada é emitido.
        if (!$errors && $valid) {
            $db->beginTransaction();
            try {
                foreach ($valid as $d) { $made = cert_issue_stored($db, $d, $adminId, $templateId); $importedIds[] = (int)$made['id']; }
                $db->commit();
                cert_log_action($db, 'lote', 'Importação de planilha: ' . count($valid) . ' certificados (' . mb_substr((string)($_FILES['csv']['name'] ?? ''), 0, 80, 'UTF-8') . ')', $importedIds, $adminId);
                $msg = count($valid) . ' certificados emitidos. Se algo ficou errado, desfaça em Histórico.';
            } catch (Throwable $e) {
                if ($db->inTransaction()) $db->rollBack();
                $importedIds = [];
                error_log('[CoBraLT] import certificados: ' . $e->getMessage());
                $errors[] = 'Erro ao importar. Nada foi emitido.';
            }
        }
    }

    if ($action === 'regen') {
        $st = $db->prepare('SELECT * FROM certificates WHERE id = ?');
        $st->execute([(int)($_POST['id'] ?? 0)]);
        $c = $st->fetch();
        if (!$c) {
            $errors[] = 'Certificado não encontrado.';
        } elseif (($c['source'] ?? '') === 'pdf') {
            $errors[] = 'Este certificado veio de um PDF enviado: para refazer, assine o PDF original de novo em "Assinar PDF pronto".';
        } else {
            cert_store_pdf($db, $c, cert_render_for($db, $c), (string)($c['source'] ?: 'padrao'));
            $msg = 'PDF refeito com o endereço atual do site. Baixe-o de novo e substitua o arquivo antigo.';
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
    $where = ' WHERE holder_name LIKE ? OR holder_email LIKE ? OR code LIKE ? OR event_name LIKE ?';
    $params = [$like, $like, $like, $like];
}
$stmt = $db->prepare('SELECT * FROM certificates' . $where . ' ORDER BY id DESC LIMIT 200');
$stmt->execute($params);
$rows = $stmt->fetchAll();
admin_page_start('Certificados', 'certificados', 'Emitir, importar em lote, baixar e revogar');
admin_alerts($msg, $errors);
if ($importedIds && class_exists('ZipArchive')): ?>
<div class="alert ok"><a class="btn-sm" href="?zip=<?= e(implode(',', $importedIds)) ?>">Baixar todos os PDFs (ZIP)</a> &nbsp; ou use o botão PDF de cada um na lista abaixo.</div>
<?php endif;
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
            <select name="template_id"><?php foreach ($templates as $i => $t): ?><option value="<?= (int)$t['id'] ?>"<?= $i === 0 ? ' selected' : '' ?>><?= e($t['name']) ?></option><?php endforeach; ?><option value="0">Layout simples (sem modelo)</option></select></div>
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
    <div class="card-header"><h3>Importar vários (planilha)</h3><a class="btn-sm outline" href="../assets/admin/exemplo-certificados.csv" download>Baixar planilha de exemplo</a></div>
    <div class="card-body">
      <p class="hint" style="margin-bottom:1rem">Planilha <strong>Excel (.xlsx)</strong> ou <strong>CSV</strong>, uma linha por pessoa. Obrigatórias: <strong>nome</strong> e <strong>evento</strong>. Opcionais: tipo, horas, data, email. O texto do certificado vem do <strong>modelo</strong> escolhido (use {nome}, {evento}, {horas}… lá). Máx. 500 linhas — se uma linha tiver erro, nada é emitido.</p>
      <button type="button" class="csv-example" id="csvExample" aria-label="Ampliar exemplo de planilha">
        <img src="../assets/admin/exemplo-csv-certificados.png" alt="Exemplo de planilha com as colunas nome, evento, tipo, horas, data e email">
        <span>🔍 Clique para ampliar</span>
      </button>
      <form method="post" enctype="multipart/form-data" style="margin-top:1rem">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="import">
        <div class="grid-form">
          <div class="field"><label>Modelo</label>
            <select name="template_id"><?php foreach ($templates as $i => $t): ?><option value="<?= (int)$t['id'] ?>"<?= $i === 0 ? ' selected' : '' ?>><?= e($t['name']) ?></option><?php endforeach; ?><option value="0">Layout simples (sem modelo)</option></select></div>
          <div class="field"><label>Planilha (.xlsx ou .csv)</label><input type="file" name="csv" accept=".xlsx,.csv,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" required></div>
        </div>
        <div class="btn-row"><button type="submit" class="btn">Importar e emitir</button></div>
      </form>
    </div>
  </section>
</div>

<section class="card">
  <div class="card-header">
    <h3>Emitidos (últimos 200)</h3>
    <form method="get" class="inline-form"><input type="search" name="q" value="<?= e($q) ?>" placeholder="Buscar por nome, e-mail, código ou evento"><button class="btn-sm" type="submit">Buscar</button></form>
  </div>
  <div class="table-wrap">
  <table>
    <thead><tr><th>Código</th><th>Nome</th><th>Evento</th><th>Tipo</th><th>Data</th><th>Status</th><th>Ações</th></tr></thead>
    <tbody>
    <?php foreach ($rows as $r): ?>
      <tr>
        <td class="mono"><a href="autenticar.php?c=<?= e(rawurlencode($r['code'])) ?>"><?= e($r['code']) ?></a></td>
        <td><strong><?= e($r['holder_name']) ?></strong><?= $r['holder_email'] ? '<br><small class="hint">' . e($r['holder_email']) . '</small>' : '' ?></td>
        <td><?= e($r['event_name']) ?></td>
        <td><?= e(cert_types()[$r['cert_type']] ?? $r['cert_type']) ?></td>
        <td><?= e(date('d/m/Y', (int)strtotime((string)$r['issued_at']))) ?></td>
        <td><span class="badge <?= e($r['status']) ?>"><?= e($r['status']) ?></span><?= $r['revoked_reason'] ? '<br><small class="hint">' . e($r['revoked_reason']) . '</small>' : '' ?></td>
        <td>
          <div class="actions">
            <a class="btn-sm outline" href="?pdf=<?= (int)$r['id'] ?>">PDF</a>
            <a class="btn-sm outline" href="<?= e(cert_verify_url($r['code'])) ?>" target="_blank" rel="noopener">Link</a>
            <?php if (($r['source'] ?? '') !== 'pdf'): ?>
            <form method="post" style="display:inline"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="regen"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>"><button class="btn-sm outline" title="Gera o PDF de novo com o endereço atual do site">Refazer PDF</button></form>
            <?php endif; ?>
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
