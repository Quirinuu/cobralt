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
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Autenticar certificado — Admin CoBraLT</title>
  <style>
    body{font-family:'Segoe UI',sans-serif;background:#F1F5F9;color:#1E293B;margin:0;padding:2rem;}
    h1{color:#002B4E;font-size:1.3rem;} h2{color:#002B4E;font-size:1rem;margin:0 0 .75rem;} a{color:#0284C7;}
    .card{background:#fff;border:1px solid #E2E8F0;border-radius:12px;padding:1.25rem;margin-bottom:1.25rem;max-width:760px;}
    input{box-sizing:border-box;padding:.55rem .7rem;border:1.5px solid #E2E8F0;border-radius:8px;font:inherit;}
    button,.btn{padding:.55rem 1rem;border:0;border-radius:8px;background:#002B4E;color:#fff;font:inherit;font-weight:600;cursor:pointer;}
    .btn.alt{background:#0284C7;display:inline-block;}
    form.row{display:flex;gap:.5rem;flex-wrap:wrap;} form.row input[type=text]{flex:1;min-width:220px;text-transform:uppercase;}
    table{border-collapse:collapse;font-size:.85rem;} th,td{text-align:left;padding:.3rem .8rem .3rem 0;}
  </style>
</head>
<body>
  <p><a href="dashboard.php">← Painel</a> · <a href="certificados.php">Certificados</a></p>
  <h1>Autenticar certificado</h1>

  <div class="card">
    <h2>Pelo código</h2>
    <form method="get" class="row">
      <input type="text" name="c" value="<?= e($input) ?>" placeholder="CBL-XXXX-XXXX-XXXX" maxlength="40" required autocomplete="off">
      <button type="submit">Verificar</button>
    </form>
  </div>

  <div class="card">
    <h2>Pelo QR code</h2>
    <div id="qr-tool" data-target="autenticar.php" hidden>
      <button type="button" id="qr-camera">Escanear com a câmera</button>
      <label class="btn alt">Enviar imagem do QR<input type="file" id="qr-file" accept="image/*" hidden></label>
      <video id="qr-video" playsinline muted style="display:none;width:100%;max-width:420px;margin-top:.75rem;border-radius:8px;"></video>
      <p id="qr-msg" role="status"></p>
    </div>
    <noscript>Ative o JavaScript para ler o QR code.</noscript>
  </div>

  <div class="card">
    <h2>Pelo arquivo PDF</h2>
    <p style="margin-top:0">Confere se o arquivo é idêntico ao original emitido. O PDF não é guardado.</p>
    <form method="post" enctype="multipart/form-data" class="row">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <input type="file" name="pdf" accept="application/pdf,.pdf" required>
      <button type="submit">Conferir PDF</button>
    </form>
  </div>

  <div style="max-width:760px;">
  <?php if (is_array($result)): cert_render_card($result, $identical, true); ?>
    <div class="card" style="margin-top:1.25rem;">
      <h2>Consultas públicas deste código (últimas 10)</h2>
      <?php if (!$history): ?><p style="margin:0">Nenhuma consulta registrada.</p><?php else: ?>
      <table><?php foreach ($history as $row): ?>
        <tr><td><?= e((string)$row['created_at']) ?></td><td><?= e((string)$row['ip']) ?></td><td><?= $row['found'] ? 'encontrado' : 'não encontrado' ?></td></tr>
      <?php endforeach; ?></table>
      <?php endif; ?>
      <p><a href="certificados.php?q=<?= e(rawurlencode((string)$result['code'])) ?>">Abrir na lista (revogar, baixar PDF)</a></p>
    </div>
  <?php elseif ($result === 'notfound'): ?>
    <div class="card" style="border-color:#F59E0B;"><strong>Não encontrado.</strong> Nenhum certificado emitido por este sistema com esses dados.</div>
  <?php elseif ($result === 'nocode'): ?>
    <div class="card" style="border-color:#F59E0B;"><strong>Sem código.</strong> Este PDF não traz um código de certificado do CoBraLT (pode ter sido impresso, convertido ou não ser nosso).</div>
  <?php elseif ($result === 'badpdf'): ?>
    <div class="card" style="border-color:#F59E0B;"><strong>Arquivo ilegível.</strong> Envie um PDF de até 2 MB.</div>
  <?php endif; ?>
  </div>

  <script src="../js/vendor/jsQR.js"></script>
  <script src="../js/verificar.js"></script>
</body>
</html>
