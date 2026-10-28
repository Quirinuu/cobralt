<?php
/**
 * admin/autenticar.php — Autenticar certificados dentro do painel (código, QR ou PDF).
 * Mostra dados completos e o histórico de consultas públicas do código; não gera log nem tem limite.
 */
declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
require_once dirname(__DIR__) . '/includes/certificate_verify.php';
require_role('superadmin', 'admin');

$db = getDB();
$input = trim((string)($_GET['c'] ?? ''));
$result = null;     // null | 'notfound' | 'nocode' | 'badpdf' | array
$identical = null;
$history = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf'])) {
    csrf_verify();
    [$bytes] = cert_read_uploaded_pdf('pdf');
    $r = $bytes === null ? ['state' => 'invalid'] : cert_verify_pdf($db, $bytes);
    if ($r['state'] === 'found') { $result = $r['cert']; $identical = $r['identical']; }
    else { $result = ['invalid' => 'badpdf', 'no_code' => 'nocode'][$r['state']] ?? 'notfound'; }
} elseif ($input !== '') {
    $code = cert_normalize_code($input);
    $result = ($code !== '' ? cert_find($db, $code) : null) ?? 'notfound';
}

if (is_array($result)) {
    $h = $db->prepare('SELECT found, ip, created_at FROM certificate_checks WHERE code_tried = ? ORDER BY id DESC LIMIT 10');
    $h->execute([$result['code']]);
    $history = $h->fetchAll();
}
admin_page_start('Autenticar certificado', 'autenticar', 'Confira um certificado pelo código, QR code ou PDF');
?>
<div class="grid-2">
  <div>
    <section class="card">
      <div class="card-header"><h3>1. Pelo código</h3></div>
      <div class="card-body">
        <form method="get" class="inline-form">
          <input type="text" name="c" value="<?= e($input) ?>" placeholder="CBL-XXXX-XXXX-XXXX" maxlength="40" required autocomplete="off" style="text-transform:uppercase">
          <button type="submit" class="btn">Verificar</button>
        </form>
      </div>
    </section>

    <section class="card">
      <div class="card-header"><h3>2. Pelo QR code</h3></div>
      <div class="card-body">
        <div id="qr-tool" data-target="autenticar.php" hidden>
          <div class="btn-row" style="margin-top:0">
            <button type="button" id="qr-camera" class="btn">Escanear com a câmera</button>
            <label class="btn outline" style="margin:0;cursor:pointer">Enviar imagem do QR<input type="file" id="qr-file" accept="image/*" hidden></label>
          </div>
          <video id="qr-video" playsinline muted style="display:none;width:100%;max-width:420px;margin-top:.75rem;border-radius:8px;"></video>
          <p id="qr-msg" role="status" class="hint" style="margin-top:.5rem"></p>
        </div>
        <noscript><p class="hint">Ative o JavaScript para ler o QR code.</p></noscript>
      </div>
    </section>

    <section class="card">
      <div class="card-header"><h3>3. Pelo arquivo PDF</h3></div>
      <div class="card-body">
        <p class="hint" style="margin-bottom:.75rem">Confere se o arquivo é idêntico ao original emitido. O PDF não é guardado.</p>
        <form method="post" enctype="multipart/form-data" class="inline-form">
          <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
          <input type="file" name="pdf" accept="application/pdf,.pdf" required>
          <button type="submit" class="btn">Conferir PDF</button>
        </form>
      </div>
    </section>
  </div>

  <div>
  <?php if (is_array($result)): cert_render_card($result, $identical, true); ?>
    <section class="card" style="margin-top:1.5rem">
      <div class="card-header"><h3>Consultas públicas deste código</h3></div>
      <div class="table-wrap">
      <?php if (!$history): ?><p class="empty">Nenhuma consulta registrada.</p><?php else: ?>
        <table><thead><tr><th>Data</th><th>IP</th><th>Resultado</th></tr></thead><tbody>
        <?php foreach ($history as $row): ?>
          <tr><td><?= e((string)$row['created_at']) ?></td><td class="mono"><?= e((string)$row['ip']) ?></td><td><?= $row['found'] ? 'encontrado' : 'não encontrado' ?></td></tr>
        <?php endforeach; ?></tbody></table>
      <?php endif; ?>
      </div>
      <div class="card-body"><a class="btn-sm outline" href="certificados.php?q=<?= e(rawurlencode((string)$result['code'])) ?>">Abrir na lista (revogar, baixar PDF)</a></div>
    </section>
  <?php elseif ($result === 'notfound'): ?>
    <div class="alert warn"><strong>Não encontrado.</strong> Nenhum certificado emitido por este sistema com esses dados.</div>
  <?php elseif ($result === 'nocode'): ?>
    <div class="alert warn"><strong>Sem código.</strong> Este PDF não traz um código de certificado do CoBraLT (pode ter sido impresso, convertido ou não ser nosso).</div>
  <?php elseif ($result === 'badpdf'): ?>
    <div class="alert warn"><strong>Arquivo ilegível.</strong> Envie um PDF de até 2 MB.</div>
  <?php else: ?>
    <div class="card"><p class="empty">O resultado da conferência aparece aqui.</p></div>
  <?php endif; ?>
  </div>
</div>
<?php
admin_page_end('<script src="../js/vendor/jsQR.js"></script><script src="../js/verificar.js"></script>');
