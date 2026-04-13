<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/layout.php';
require_once dirname(__DIR__) . '/includes/posts_helpers.php';

try {
    $db    = getPublicDB();
    $posts = get_posts_by_tipo($db, 'eventos');
} catch (PDOException $e) {
    $posts = [];
}

layout_head('Eventos', 'Agenda completa de eventos e congressos do CoBraLT.');
layout_header('eventos');
?>

<main id="main-content">

<div class="page-hero">
  <div class="page-hero-inner">
    <nav class="breadcrumb">
      <a href="../index.php">Início</a><span>›</span><span>Eventos</span>
    </nav>
    <div class="page-hero-label">Agenda CoBraLT</div>
    <h1 class="page-hero-title">Eventos</h1>
    <p class="page-hero-sub">Agenda completa de eventos e congressos do CoBraLT.</p>
    <div class="region-stats">
      <div class="region-stat"><div class="region-stat-value"><?= count($posts) ?></div><div class="region-stat-label">publicações</div></div>
      <div class="region-stat"><div class="region-stat-value">2026</div><div class="region-stat-label">ano em curso</div></div>
      <div class="region-stat"><div class="region-stat-value">180+</div><div class="region-stat-label">ligas na rede</div></div>
    </div>
  </div>
</div>

<section class="section" style="padding-top:3rem;">
  <div class="section-inner">
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:1.5rem;">
      <?php render_posts_grid($posts, 'eventos'); ?>
    </div>
  </div>
</section>

</main>

<?php layout_footer('../'); ?>
