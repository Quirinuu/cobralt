<?php
/**
 * pages/verificar.php — Verificador público de certificados emitidos pelo CoBraLT.
 * Funciona sem JS: formulário GET /verificar?c=CODIGO (destino do QR do PDF).
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/layout.php';
require_once dirname(__DIR__) . '/includes/certificate_verify.php';
require_once dirname(__DIR__) . '/includes/certificate_issue.php';

header('X-Robots-Tag: noindex');
header('Cache-Control: no-store');

// Download do PDF de um certificado válido (link do resultado da busca).
if (isset($_GET['dl'])) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    try {
        $db = getPublicDB();
        cert_ensure_schema($db);
        $code = cert_normalize_code((string)$_GET['dl']);
        $cert = (!cert_check_rate_limited($db, $ip) && $code !== '') ? cert_find($db, $code) : null;
        if ($cert) { cert_log_check($db, $code, true, $ip); }
        if ($cert && $cert['status'] === 'valido') {
            $bytes = cert_get_pdf($db, $cert);
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="certificado-' . $cert['code'] . '.pdf"');
            header('Content-Length: ' . strlen($bytes));
            echo $bytes;
            exit;
        }
    } catch (Throwable $e) {
        error_log('[CoBraLT] download certificado: ' . $e->getMessage());
    }
    header('Location: verificar');
    exit;
}

$q       = trim((string)($_GET['q'] ?? ''));
$matches = null;  // lista de certificados da busca por nome/e-mail
$input  = trim((string)($_GET['c'] ?? ''));
$result = null;   // null | 'limited' | 'notfound' | 'error' | 'badpdf' | 'nocode' | array do certificado
$identical = null; // true/false só quando a consulta foi por PDF
$isPdf = $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf']);

if ($input !== '' || $isPdf || $q !== '') {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    try {
        $db = getPublicDB();
        cert_ensure_schema($db);
        if (cert_check_rate_limited($db, $ip)) {
            $result = 'limited';
        } elseif ($q !== '' && $input === '' && !$isPdf) {
            if (!cert_public_query_ok($q)) {
                $result = 'shortq';
            } else {
                $matches = cert_search($db, $q, false, 20);
                cert_log_check($db, 'busca', $matches !== [], $ip); // não guarda o que foi digitado
            }
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

layout_head('Autenticar certificados', 'Confirme a autenticidade de um certificado emitido pelo CoBraLT.');
layout_header('verificar', '../', '', '../pages/');
?>

<main id="main-content">
<div class="page-hero">
  <div class="page-hero-inner">
    <nav class="breadcrumb"><a href="../">Início</a><span>›</span><span>Autenticar certificados</span></nav>
    <div class="page-hero-label">Autenticidade</div>
    <h1 class="page-hero-title">Autenticar certificados</h1>
    <p class="page-hero-sub">Digite o código impresso no certificado (ou leia o QR code) para confirmar que ele foi emitido pelo CoBraLT.</p>
  </div>
</div>

<section class="section" style="padding-top:3rem;">
  <div class="section-inner" style="max-width:680px;">
    <p style="margin-bottom:1.5rem;">Escolha como conferir. Qualquer pessoa pode verificar — não é preciso login.</p>

    <h2 style="font-size:1.05rem;margin:0 0 .5rem;">Encontrar meu certificado</h2>
    <p style="margin:0 0 .5rem;">Digite seu <strong>nome</strong> (nome e sobrenome, pelo menos 6 letras) ou o <strong>e-mail completo</strong> usado na inscrição para baixar seu certificado.</p>
    <form method="get" action="verificar" style="display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:.5rem;">
      <label for="q" style="position:absolute;left:-9999px;">Nome ou e-mail</label>
      <input type="text" id="q" name="q" value="<?= h($q) ?>" placeholder="Seu nome ou e-mail" maxlength="120" autocomplete="off" required
             style="flex:1;min-width:220px;padding:.8rem 1rem;border:1.5px solid var(--slate-200);border-radius:8px;font:inherit;">
      <button type="submit" class="btn btn-primary">Pesquisar</button>
    </form>
    <?php if ($matches !== null): ?>
      <div aria-live="polite" style="margin:1rem 0 1.5rem;">
      <?php if (!$matches): ?>
        <p>Nenhum certificado encontrado. Confira a grafia do nome ou use o e-mail completo.</p>
      <?php else: $types = cert_types(); ?>
        <p style="margin:0 0 .75rem;"><strong><?= count($matches) ?></strong> certificado(s) encontrado(s):</p>
        <?php foreach ($matches as $m): $valid = $m['status'] === 'valido'; $d = DateTime::createFromFormat('Y-m-d', (string)$m['issued_at']); ?>
          <div style="border:1.5px solid <?= $valid ? '#10B981' : '#EF4444' ?>;border-radius:12px;padding:1rem 1.25rem;background:#fff;margin-bottom:.75rem;display:flex;gap:1rem;flex-wrap:wrap;align-items:center;justify-content:space-between;">
            <div>
              <strong><?= h($m['holder_name']) ?></strong><br>
              <span><?= h($m['event_name']) ?> · <?= h($types[$m['cert_type']] ?? $m['cert_type']) ?><?= $m['hours'] ? ' · ' . (int)$m['hours'] . ' h' : '' ?> · <?= h($d ? $d->format('d/m/Y') : (string)$m['issued_at']) ?></span><br>
              <small style="color:<?= $valid ? '#047857' : '#B91C1C' ?>;"><?= $valid ? 'Certificado válido' : 'Certificado revogado' ?> · <?= h($m['code']) ?></small>
            </div>
            <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
              <?php if ($valid): ?><a class="btn btn-primary" href="verificar?dl=<?= h(rawurlencode($m['code'])) ?>">Baixar PDF</a><?php endif; ?>
              <a class="btn btn-secondary" href="verificar?c=<?= h(rawurlencode($m['code'])) ?>">Ver autenticidade</a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
      </div>
    <?php endif; ?>

    <h2 style="font-size:1.05rem;margin:2rem 0 .5rem;">Ou autenticar um certificado</h2>
    <h3 style="font-size:.95rem;margin:0 0 .5rem;">1. Pelo código</h3>
    <form method="get" action="verificar" style="display:flex;gap:.75rem;flex-wrap:wrap;">
      <label for="c" style="position:absolute;left:-9999px;">Código do certificado</label>
      <input type="text" id="c" name="c" value="<?= h($input) ?>" placeholder="CBL-XXXX-XXXX-XXXX" maxlength="40" autocomplete="off" required
             style="flex:1;min-width:220px;padding:.8rem 1rem;border:1.5px solid var(--slate-200);border-radius:8px;font:inherit;text-transform:uppercase;">
      <button type="submit" class="btn btn-primary">Verificar</button>
    </form>

    <h3 style="font-size:.95rem;margin:2rem 0 .5rem;">2. Pelo QR code</h3>
    <p style="margin:0 0 .5rem;">Aponte a câmera do celular para o QR do certificado, ou use as opções abaixo (a imagem é lida no seu aparelho e não é enviada).</p>
    <div id="qr-tool" data-target="verificar" hidden>
      <button type="button" id="qr-camera" class="btn btn-secondary">Escanear com a câmera</button>
      <label class="btn btn-secondary" style="cursor:pointer;">Enviar imagem do QR<input type="file" id="qr-file" accept="image/*" hidden></label>
      <video id="qr-video" playsinline muted style="display:none;width:100%;max-width:420px;margin-top:.75rem;border-radius:8px;"></video>
      <p id="qr-msg" role="status" style="margin:.5rem 0 0;"></p>
    </div>

    <h3 style="font-size:.95rem;margin:2rem 0 .5rem;">3. Pelo arquivo PDF</h3>
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
    <?php elseif ($result === 'shortq'): ?>
      <p>Digite seu nome e sobrenome (pelo menos 6 letras) ou o e-mail completo.</p>
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
