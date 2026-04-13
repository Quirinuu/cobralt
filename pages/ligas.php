<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/layout.php';

try {
    $db = getPublicDB();
    $ligas = $db->query(
        "SELECT emoji, nome, descricao, contagem, link
         FROM ligas_regioes WHERE ativo = 1 ORDER BY ordem ASC"
    )->fetchAll();
    $total = array_sum(array_map(fn($l) => (int)$l['contagem'], $ligas));
} catch (PDOException $e) {
    $ligas = [];
    $total = 0;
}

layout_head('Ligas Filiadas', 'Rede nacional de ligas acadêmicas de trauma filiadas ao CoBraLT.');
layout_header('ligas');
?>

<main id="main-content">

<div class="page-hero">
  <div class="page-hero-inner">
    <nav class="breadcrumb">
      <a href="../index.php">Início</a><span>›</span><span>Ligas Filiadas</span>
    </nav>
    <div class="page-hero-label">Rede Nacional</div>
    <h1 class="page-hero-title">Ligas Filiadas</h1>
    <p class="page-hero-sub">180+ instituições organizadas em <?= count($ligas) ?> ligas regionais, cobrindo todo o território nacional.</p>
    <div class="region-stats">
      <div class="region-stat"><div class="region-stat-value">180+</div><div class="region-stat-label">instituições filiadas</div></div>
      <div class="region-stat"><div class="region-stat-value"><?= count($ligas) ?></div><div class="region-stat-label">ligas regionais</div></div>
      <div class="region-stat"><div class="region-stat-value">5</div><div class="region-stat-label">regiões do Brasil</div></div>
      <div class="region-stat"><div class="region-stat-value">1999</div><div class="region-stat-label">fundação do CoBraLT</div></div>
    </div>
  </div>
</div>

<section class="section" style="padding-top:3rem;">
  <div class="section-inner">
    <div class="leagues-grid">
      <?php if (empty($ligas)): ?>
        <p style="color:var(--slate-400);grid-column:1/-1;text-align:center;">Nenhuma liga cadastrada.</p>
      <?php else: foreach ($ligas as $i => $liga): ?>
      <div class="league-card" data-animate data-animate-delay="<?= $i ?>">
        <div class="league-icon"><?= h($liga['emoji']) ?></div>
        <h3><?= h($liga['nome']) ?></h3>
        <p><?= h($liga['descricao']) ?></p>
        <span class="league-count"><?= h($liga['contagem']) ?></span>
        <a href="<?= h($liga['link']) ?>" class="news-link" style="margin-top:.75rem;display:inline-flex;">Ver ligas →</a>
      </div>
      <?php endforeach; endif; ?>
    </div>

    <div style="text-align:center;margin-top:3rem;padding-top:2rem;border-top:1px solid var(--slate-200);" data-animate>
      <p style="color:var(--slate-500);margin-bottom:1.25rem;">Quer filiar sua liga ao CoBraLT?</p>
      <a href="../index.php#filiacao" class="btn btn-primary">Filiar minha liga</a>
    </div>
  </div>
</section>

</main>

<?php layout_footer('../'); ?>
