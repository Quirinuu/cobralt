<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/layout.php';
require_once dirname(__DIR__) . '/includes/page_builder.php';

if (pb_render_managed_page_if_exists('boletimes', 'boletimes')) { exit; }

$editions = [
  [
    'title' => 'Trauma BoleTIME - Maio 2026',
    'period' => '2º trimestre/2026',
    'badge' => 'Edição 1',
    'href' => 'boletime-maio-2026.php',
    'cover' => '../assets/img/boletimes/trauma-boletime-maio-2026-cover.png',
    'summary' => 'Edição inaugural com os 23 anos do CoBraLT, entrevista com o Prof. Dr. Gustavo Pereira Fraga, ações das ligas, CoLT/COTREM, ECTES e agenda de eventos.',
  ],
];

layout_head('Trauma BoleTIME', 'Boletim trimestral do CoBraLT com notícias, entrevistas, agenda e ações das ligas do trauma.');
layout_header('boletimes');
?>

<main id="main-content">

<div class="page-hero">
  <div class="page-hero-inner">
    <nav class="breadcrumb">
      <a href="../index.php">Início</a><span>›</span><span>Trauma BoleTIME</span>
    </nav>
    <div class="page-hero-label">Boletim CoBraLT</div>
    <h1 class="page-hero-title">Trauma BoleTIME</h1>
    <p class="page-hero-sub">Publicação trimestral do CoBraLT com notícias das ligas, entrevistas, agenda científica, campanhas, projetos e registros da atuação nacional em trauma e emergência.</p>
    <div class="region-stats">
      <div class="region-stat"><div class="region-stat-value"><?= count($editions) ?></div><div class="region-stat-label">edição disponível</div></div>
      <div class="region-stat"><div class="region-stat-value">3</div><div class="region-stat-label">meses por ciclo</div></div>
      <div class="region-stat"><div class="region-stat-value">2026</div><div class="region-stat-label">ano de publicação</div></div>
    </div>
  </div>
</div>

<section class="section" style="padding-top:3rem;">
  <div class="section-inner">
    <div class="boletime-feature" data-animate>
      <div class="boletime-feature-head">
        <div>
          <span class="section-label">Edições</span>
          <h3>Arquivo do Trauma BoleTIME</h3>
          <p>Cada card leva para a página da edição, com resumo, leitura online em PDF, tela cheia e download.</p>
        </div>
      </div>

      <div class="boletime-grid">
        <?php foreach ($editions as $edition): ?>
        <a href="<?= htmlspecialchars($edition['href']) ?>" class="boletime-card" aria-label="Abrir <?= htmlspecialchars($edition['title']) ?>">
          <div class="boletime-card-media">
            <img src="<?= htmlspecialchars($edition['cover']) ?>" alt="Capa da edição <?= htmlspecialchars($edition['title']) ?>" loading="lazy">
          </div>
          <div class="boletime-card-body">
            <span class="boletime-badge"><?= htmlspecialchars($edition['badge']) ?> · <?= htmlspecialchars($edition['period']) ?></span>
            <h4><?= htmlspecialchars($edition['title']) ?></h4>
            <p><?= htmlspecialchars($edition['summary']) ?></p>
            <span class="boletime-card-link">Ver edição <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

</main>

<?php layout_footer('../'); ?>
