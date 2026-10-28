<?php
/**
 * pages/verificar.php — Verificador público de certificados emitidos pelo CoBraLT.
 * Funciona sem JS: formulário GET /verificar?c=CODIGO (destino do QR do PDF).
 */
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/layout.php';
require_once dirname(__DIR__) . '/includes/certificates.php';

header('X-Robots-Tag: noindex');
header('Cache-Control: no-store');

$input  = trim((string)($_GET['c'] ?? ''));
$result = null;   // null = nada consultado | 'invalid' | 'limited' | 'notfound' | array do certificado
if ($input !== '') {
    $ip   = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $code = cert_normalize_code($input);
    try {
        $db = getPublicDB();
        if (cert_check_rate_limited($db, $ip)) {
            $result = 'limited';
        } else {
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
  <div class="section-inner" style="max-width:640px;">
    <form method="get" action="verificar" style="display:flex;gap:.75rem;flex-wrap:wrap;">
      <label for="c" style="position:absolute;left:-9999px;">Código do certificado</label>
      <input type="text" id="c" name="c" value="<?= h($input) ?>" placeholder="CBL-XXXX-XXXX-XXXX" maxlength="40" autocomplete="off" required
             style="flex:1;min-width:220px;padding:.8rem 1rem;border:1.5px solid var(--slate-200);border-radius:8px;font:inherit;text-transform:uppercase;">
      <button type="submit" class="btn btn-primary">Verificar</button>
    </form>

    <div aria-live="polite" style="margin-top:2rem;">
    <?php if (is_array($result)):
        $valid = $result['status'] === 'valido';
        $date  = DateTime::createFromFormat('Y-m-d', (string)$result['issued_at']);
        $types = cert_types(); ?>
      <div style="border:2px solid <?= $valid ? '#10B981' : '#EF4444' ?>;border-radius:12px;padding:1.5rem;background:#fff;">
        <h2 style="font-family:var(--font-display);color:<?= $valid ? '#047857' : '#B91C1C' ?>;margin-bottom:1rem;">
          <?= $valid ? 'Certificado válido' : 'Certificado revogado' ?>
        </h2>
        <dl style="display:grid;grid-template-columns:max-content 1fr;gap:.4rem 1.25rem;">
          <dt><strong>Nome</strong></dt><dd><?= h($result['holder_name']) ?></dd>
          <dt><strong>Evento</strong></dt><dd><?= h($result['event_name']) ?></dd>
          <dt><strong>Tipo</strong></dt><dd><?= h($types[$result['cert_type']] ?? $result['cert_type']) ?></dd>
          <?php if ($result['hours']): ?><dt><strong>Carga horária</strong></dt><dd><?= (int)$result['hours'] ?> h</dd><?php endif; ?>
          <dt><strong>Emissão</strong></dt><dd><?= h($date ? $date->format('d/m/Y') : (string)$result['issued_at']) ?></dd>
          <dt><strong>Código</strong></dt><dd><?= h($result['code']) ?></dd>
          <?php if (!$valid && $result['revoked_at']): ?><dt><strong>Revogado em</strong></dt><dd><?= h(substr((string)$result['revoked_at'], 0, 10)) ?></dd><?php endif; ?>
        </dl>
      </div>
    <?php elseif ($result === 'notfound'): ?>
      <div style="border:2px solid #F59E0B;border-radius:12px;padding:1.5rem;background:#fff;">
        <h2 style="font-family:var(--font-display);color:#B45309;margin-bottom:.5rem;">Certificado não encontrado</h2>
        <p>Não há certificado emitido pelo CoBraLT com este código. Confira se digitou corretamente.</p>
      </div>
    <?php elseif ($result === 'limited'): ?>
      <p>Muitas consultas em pouco tempo. Aguarde alguns minutos e tente novamente.</p>
    <?php elseif ($result === 'error'): ?>
      <p>Não foi possível consultar agora. Tente novamente em instantes.</p>
    <?php endif; ?>
    </div>
  </div>
</section>
</main>

<?php layout_footer('../'); ?>
