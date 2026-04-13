<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/layout.php';
require_once dirname(__DIR__) . '/includes/posts_helpers.php';

try {
    $db    = getPublicDB();
    $posts = get_posts_by_tipo($db, 'educacao');
} catch (PDOException $e) {
    $posts = [];
}

layout_head('Educação', 'Conteúdo educacional e capacitações das ligas do CoBraLT.');
layout_header('educacao');
?>

<main id="main-content">

<div class="page-hero">
  <div class="page-hero-inner">
    <nav class="breadcrumb">
      <a href="../index.php">Início</a><span>›</span><span>Educação</span>
    </nav>
    <div class="page-hero-label">CoBraLT Educa</div>
    <h1 class="page-hero-title">Educação</h1>
    <p class="page-hero-sub">Conteúdo educacional e capacitações das ligas do CoBraLT.</p>
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
      <?php render_posts_grid($posts, 'educacao'); ?>
    </div>
  </div>
</section>

</main>

<?php layout_footer('../'); ?>
