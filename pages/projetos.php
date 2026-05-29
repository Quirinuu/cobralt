<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/layout.php';
require_once dirname(__DIR__) . '/includes/page_builder.php';

if (pb_render_managed_page_if_exists('projetos', 'projetos')) { exit; }

layout_head('Projetos', 'Programas e projetos oficiais das ligas filiadas ao CoBraLT.');
layout_header('projetos');
?>

<main id="main-content">

<div class="page-hero">
  <div class="page-hero-inner">
    <nav class="breadcrumb">
      <a href="../index.php">Início</a><span>›</span><span>Projetos</span>
    </nav>
    <div class="page-hero-label">Iniciativas CoBraLT</div>
    <h1 class="page-hero-title">Projetos</h1>
    <p class="page-hero-sub">Programas e projetos oficiais das ligas filiadas ao CoBraLT.</p>
    <div class="region-stats">
      <div class="region-stat"><div class="region-stat-value">2</div><div class="region-stat-label">programas oficiais</div></div>
      <div class="region-stat"><div class="region-stat-value">2026</div><div class="region-stat-label">ciclo vigente</div></div>
      <div class="region-stat"><div class="region-stat-value">180+</div><div class="region-stat-label">ligas na rede</div></div>
    </div>
  </div>
</div>

<section class="section" style="padding-top:3rem;">
  <div class="section-inner">
    <div class="programs-feature" data-animate>
      <div class="programs-feature-head">
        <div>
          <span class="section-label">Programas oficiais</span>
          <h3>Editais e adesões abertas</h3>
          <p>Documentos de referência para ligas filiadas, com leitura online, tela cheia e download dos PDFs.</p>
        </div>
      </div>

      <div class="programs-feature-grid">
        <a href="programa-salvando-vidas-2026.php" class="program-card program-card--salvando" aria-label="Abrir página do Programa Salvando Vidas 2026">
          <div class="program-card-media">
            <img src="../assets/img/programas/salvando-vidas-2026-cover.png" alt="Capa do edital do Programa Salvando Vidas 2026" loading="lazy">
          </div>
          <div class="program-card-body">
            <span class="program-badge">Maio-dez/2026</span>
            <h4>Programa Salvando Vidas 2026</h4>
            <p>Projeto social de prevenção, primeiros socorros e educação em urgência, emergência e trauma.</p>
            <span class="program-card-link">Ver edital e PDF <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
          </div>
        </a>

        <a href="programa-party-brasil-2026.php" class="program-card program-card--party" aria-label="Abrir página do Programa P.A.R.T.Y. Brasil 2026">
          <div class="program-card-media">
            <img src="../assets/img/programas/party-brasil-2026-cover.png" alt="Capa do edital do Programa P.A.R.T.Y. Brasil 2026" loading="lazy">
          </div>
          <div class="program-card-body">
            <span class="program-badge">Edital 10/04/2026</span>
            <h4>Programa P.A.R.T.Y. Brasil 2026</h4>
            <p>Estratégia de prevenção primária do trauma em jovens, com edital de adesão e ficha de inscrição.</p>
            <span class="program-card-link">Ver documentos <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
          </div>
        </a>
      </div>
    </div>
  </div>
</section>

</main>

<?php layout_footer('../'); ?>
