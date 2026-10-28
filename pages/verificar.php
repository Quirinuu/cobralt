<?php
/**
 * pages/verificar.php — Verificador público de certificados emitidos pelo CoBraLT.
 * Funciona sem JS: formulário GET /verificar?c=CODIGO (destino do QR do PDF).
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/layout.php';
require_once dirname(__DIR__) . '/includes/certificate_verify.php';

header('X-Robots-Tag: noindex');
header('Cache-Control: no-store');

$input  = trim((string)($_GET['c'] ?? ''));
$result = null;   // null | 'limited' | 'notfound' | 'error' | 'badpdf' | 'nocode' | array do certificado
$identical = null; // true/false só quando a consulta foi por PDF
$isPdf = $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf']);

if ($input !== '' || $isPdf) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    try {
        $db = getPublicDB();
        if (cert_check_rate_limited($db, $ip)) {
            $result = 'limited';
        } elseif ($isPdf) {
            [$bytes, $err] = cert_read_uploaded_pdf('pdf');
            $r = $bytes === null ? ['state' => 'invalid'] : cert_verify_pdf($db, $bytes);
            cert_log_check($db, $r['code'] ?? 'pdf', ($r['state'] ?? '') === 'found', $ip);
            if (($r['state'] ?? '') === 'found') { $result = $r['cert']; $identical = $r['identical']; }
            else { $result = ['invalid' => 'badpdf', 'no_code' => 'nocode'][$r['state']] ?? 'notfound'; }
        } else {
            $code = cert_normalize_code($input);
            $cert = $code !== '' ? cert_find($db, $code) : null;
            cert_log_check($db, $code !== '' ? $code : $input, $cert !== null, $ip);
            // Mesma resposta para formato inválido e código inexistente (não dá pistas).
            $result = $cert ?? 'notfound';
        }
    } catch (Throwable $e) {
        error_log('[CoBraLT] verificar: ' . $e->getMessage());
        $result = 'error';
    }
}

layout_head('Verificar certificado', 'Confirme a autenticidade de um certificado emitido pelo CoBraLT.');
layout_header('', '../', '', '../pages/');
?>

<main id="main-content">
<div class="page-hero">
  <div class="page-hero-inner">
    <nav class="breadcrumb"><a href="../">Início</a><span>›</span><span>Verificar certificado</span></nav>
    <div class="page-hero-label">Autenticidade</div>
    <h1 class="page-hero-title">Verificar certificado</h1>
    <p class="page-hero-sub">Digite o código impresso no certificado (ou leia o QR code) para confirmar que ele foi emitido pelo CoBraLT.</p>
  </div>
</div>

<section class="section" style="padding-top:3rem;">
  <div class="section-inner" style="max-width:680px;">
    <p style="margin-bottom:1.5rem;">Escolha como conferir. Qualquer pessoa pode verificar — não é preciso login.</p>

    <h2 style="font-size:1.05rem;margin:0 0 .5rem;">1. Pelo código</h2>
    <form method="get" action="verificar" style="display:flex;gap:.75rem;flex-wrap:wrap;">
      <label for="c" style="position:absolute;left:-9999px;">Código do certificado</label>
      <input type="text" id="c" name="c" value="<?= h($input) ?>" placeholder="CBL-XXXX-XXXX-XXXX" maxlength="40" autocomplete="off" required
             style="flex:1;min-width:220px;padding:.8rem 1rem;border:1.5px solid var(--slate-200);border-radius:8px;font:inherit;text-transform:uppercase;">
      <button type="submit" class="btn btn-primary">Verificar</button>
    </form>

    <h2 style="font-size:1.05rem;margin:2rem 0 .5rem;">2. Pelo QR code</h2>
    <p style="margin:0 0 .5rem;">Aponte a câmera do celular para o QR do certificado, ou use as opções abaixo (a imagem é lida no seu aparelho e não é enviada).</p>
    <div id="qr-tool" data-target="verificar" hidden>
      <button type="button" id="qr-camera" class="btn btn-secondary">Escanear com a câmera</button>
      <label class="btn btn-secondary" style="cursor:pointer;">Enviar imagem do QR<input type="file" id="qr-file" accept="image/*" hidden></label>
      <video id="qr-video" playsinline muted style="display:none;width:100%;max-width:420px;margin-top:.75rem;border-radius:8px;"></video>
      <p id="qr-msg" role="status" style="margin:.5rem 0 0;"></p>
    </div>

    <h2 style="font-size:1.05rem;margin:2rem 0 .5rem;">3. Pelo arquivo PDF</h2>
    <p style="margin:0 0 .5rem;">Envie o PDF recebido. Conferimos se ele é idêntico ao original emitido. O arquivo é analisado e <strong>não é guardado</strong>.</p>
    <form method="post" action="verificar" enctype="multipart/form-data" style="display:flex;gap:.75rem;flex-wrap:wrap;">
      <label for="pdf" style="position:absolute;left:-9999px;">Arquivo PDF</label>
      <input type="file" id="pdf" name="pdf" accept="application/pdf,.pdf" required style="flex:1;min-width:220px;">
      <button type="submit" class="btn btn-primary">Conferir PDF</button>
    </form>

    <div aria-live="polite" style="margin-top:2rem;">
    <?php if (is_array($result)): cert_render_card($result, $identical); ?>
    <?php elseif ($result === 'notfound'): ?>
      <div style="border:2px solid #F59E0B;border-radius:12px;padding:1.5rem;background:#fff;">
        <h2 style="font-family:var(--font-display);color:#B45309;margin-bottom:.5rem;">Certificado não encontrado</h2>
        <p>Não há certificado emitido pelo CoBraLT com estes dados. Confira se digitou corretamente.</p>
      </div>
    <?php elseif ($result === 'nocode'): ?>
      <p>Este PDF não traz um código de certificado do CoBraLT. Se ele foi impresso ou convertido, use o código ou o QR code impresso nele.</p>
    <?php elseif ($result === 'badpdf'): ?>
      <p>Não foi possível ler o arquivo. Envie um PDF de até 2 MB.</p>
    <?php elseif ($result === 'limited'): ?>
      <p>Muitas consultas em pouco tempo. Aguarde alguns minutos e tente novamente.</p>
    <?php elseif ($result === 'error'): ?>
      <p>Não foi possível consultar agora. Tente novamente em instantes.</p>
    <?php endif; ?>
    </div>
  </div>
</section>
<script src="../js/vendor/jsQR.js"></script>
<script src="../js/verificar.js"></script>
</main>

<?php layout_footer('../'); ?>
