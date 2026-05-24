<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/layout.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/colt_editions.php';
require_once dirname(__DIR__) . '/includes/page_builder.php';

if (pb_render_managed_page_if_exists('colts', 'eventos')) { exit; }

$editions = colt_editions_newest_first();
$editionsJson = htmlspecialchars(
    json_encode($editions, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]',
    ENT_QUOTES | ENT_SUBSTITUTE,
    'UTF-8'
);

layout_head('Memórias dos CoLTs', 'Galeria histórica dos Congressos Brasileiros das Ligas do Trauma.');
layout_header('eventos');
?>

<main id="main-content">

<div class="page-hero">
  <div class="page-hero-inner">
    <nav class="breadcrumb">
      <a href="../index.php">Início</a><span>›</span><a href="eventos.php">Eventos</a><span>›</span><span>Memórias dos CoLTs</span>
    </nav>
    <div class="page-hero-label">Acervo CoBraLT</div>
    <h1 class="page-hero-title">Memórias dos CoLTs</h1>
    <p class="page-hero-sub">Um acervo visual dos Congressos Brasileiros das Ligas do Trauma, organizado por edição.</p>
    <div class="region-stats">
      <div class="region-stat"><div class="region-stat-value"><?= count($editions) ?></div><div class="region-stat-label">edições</div></div>
      <div class="region-stat"><div class="region-stat-value">1999</div><div class="region-stat-label">primeiro registro</div></div>
      <div class="region-stat"><div class="region-stat-value">CoLT</div><div class="region-stat-label">memória viva</div></div>
    </div>
  </div>
</div>

<section class="section colt-gallery-page">
  <div class="section-inner">
    <div class="section-header" data-animate>
      <div class="divider" aria-hidden="true"></div>
      <span class="section-label">Fotos históricas</span>
      <h2 class="section-title">Galeria por edição</h2>
      <p class="section-subtitle">Clique em qualquer imagem para visualizar em tela cheia.</p>
    </div>

    <div
      id="coltGalleryRoot"
      class="colt-gallery-root is-loading"
      data-bases="../assets/img/Imagens Colts/|../assets/img/colts/|../assets/img/|../"
      data-max-photos="300"
      data-editions='<?= $editionsJson ?>'
    >
      <p class="colt-gallery-loading">Preparando acervo fotográfico...</p>
    </div>
    <p id="coltGalleryEmpty" class="colt-gallery-empty" hidden>As fotos desta seção ainda serão publicadas.</p>
  </div>
</section>

</main>

<?php layout_footer('../', ['js/colt-gallery.js']); ?>
