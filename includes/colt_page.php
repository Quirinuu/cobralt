<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/layout.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/colt_editions.php';

$edition = find_colt_edition($coltSlug ?? '');
if (!$edition) {
    header('HTTP/1.0 404 Not Found');
    layout_head('CoLT não encontrado', 'Edição do CoLT não encontrada.');
    layout_header('eventos');
    echo '<main id="main-content"><div style="text-align:center;padding:6rem 1rem;"><h1 style="font-family:var(--font-display);color:var(--navy);">CoLT não encontrado</h1><p style="color:var(--slate-500);margin:1rem 0 2rem;">A edição solicitada não existe no acervo.</p><a href="eventos.php#realizados" class="btn btn-secondary">← Voltar para eventos</a></div></main>';
    layout_footer('../');
    exit;
}

$title = trim($edition['edition'] . ($edition['year'] ? ' ' . $edition['year'] : ''));
$place = $edition['place'] ?: 'Brasil';
$heroMeta = trim(($edition['year'] ?: 'Acervo') . ' · ' . $place);
$editionsJson = htmlspecialchars(
    json_encode([$edition], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]',
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
);

layout_head($title, $title . ' — memória histórica dos Congressos Brasileiros das Ligas do Trauma.');
layout_header('eventos');
?>

<main id="main-content">

<div class="page-hero" style="background:linear-gradient(135deg,var(--navy-dark) 0%,#002a55 50%,var(--navy-light) 100%);">
  <div class="page-hero-inner">
    <nav class="breadcrumb">
      <a href="../index.php">Início</a><span>›</span>
      <a href="eventos.php">Eventos e Congressos</a><span>›</span>
      <span><?= h($title) ?></span>
    </nav>
    <div class="page-hero-label" style="display:flex;align-items:center;gap:6px;">
      <span>🏆</span> Evento Histórico · <?= h($heroMeta) ?>
    </div>
    <h1 class="page-hero-title"><?= h($title) ?></h1>
    <p class="page-hero-sub">Memória visual do <?= h($edition['edition']) ?>, parte da trajetória dos Congressos Brasileiros das Ligas do Trauma.</p>
    <div class="region-stats">
      <div class="region-stat"><div class="region-stat-value"><?= h((string)$edition['number']) ?></div><div class="region-stat-label">edição</div></div>
      <div class="region-stat"><div class="region-stat-value"><?= h($edition['year'] ?: '—') ?></div><div class="region-stat-label">ano</div></div>
      <div class="region-stat"><div class="region-stat-value"><?= h($place) ?></div><div class="region-stat-label">local</div></div>
      <div class="region-stat"><div class="region-stat-value">auto</div><div class="region-stat-label">galeria</div></div>
    </div>
  </div>
</div>

<section class="section colt-gallery-page" style="padding-top:3rem;">
  <div class="section-inner" style="max-width:1040px;">
    <div class="section-header centered" data-animate>
      <div class="divider" style="margin:0 auto 1rem;" aria-hidden="true"></div>
      <span class="section-label">Galeria</span>
      <h2 class="section-title">Memórias do <?= h($edition['edition']) ?></h2>
      <p class="section-subtitle">As imagens são carregadas automaticamente da pasta <strong><?= h($edition['folder']) ?></strong>, em ordem numérica.</p>
    </div>

    <div
      id="coltGalleryRoot"
      class="colt-gallery-root is-loading"
      data-bases="../assets/img/Imagens Colts/|../assets/img/colts/|../assets/img/|../"
      data-max-photos="300"
      data-editions='<?= $editionsJson ?>'
    >
      <p class="colt-gallery-loading">Carregando fotos disponíveis...</p>
    </div>
    <p id="coltGalleryEmpty" class="colt-gallery-empty" hidden>Nenhuma imagem encontrada para esta edição.</p>
  </div>
</section>

<section class="section" style="padding-top:2rem;">
  <div class="section-inner" style="max-width:860px;">
    <div class="colt-memory-card" data-animate>
      <span class="section-label">Sobre esta edição</span>
      <h2><?= h($title) ?></h2>
      <p>Esta página preserva o acervo fotográfico do <?= h($edition['edition']) ?> dentro da história do CoBraLT. A galeria não exige cadastro manual de fotos: basta manter os arquivos como <strong>1.png</strong>, <strong>2.png</strong>, <strong>3.png</strong> e assim por diante dentro da pasta da edição.</p>
      <div class="colt-memory-actions">
        <a href="eventos.php#realizados" class="btn btn-secondary">← Voltar aos eventos</a>
        <a href="colts.php" class="btn btn-primary">Ver acervo completo</a>
      </div>
    </div>
  </div>
</section>

</main>

<?php layout_footer('../', ['js/colt-gallery.js']); ?>
