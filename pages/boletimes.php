<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/layout.php';
require_once dirname(__DIR__) . '/includes/page_builder.php';

if (pb_render_managed_page_if_exists('boletimes', 'boletimes')) { exit; }

$editions = [
  [
    'title' => 'Trauma BoleTIME - Agosto 2026',
    'period' => '3º trimestre/2026',
    'badge' => 'Edição 2',
    'href' => 'boletime-agosto-2026',
    'cover' => '../assets/img/boletimes/trauma-boletime-agosto-2026-cover.png',
    'summary' => 'CoLT e COTREM reúnem mais de 600 participantes, entrevista com o Dr. Wellington Santos, 5ª Onda Amarela, Congresso Brasileiro de Queimaduras e a conquista do CNPJ próprio.',
  ],
  [
    'title' => 'Trauma BoleTIME - Maio 2026',
    'period' => '2º trimestre/2026',
    'badge' => 'Edição 1',
    'href' => 'boletime-maio-2026',
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
      <a href="../index">Início</a><span>›</span><span>Trauma BoleTIME</span>
    </nav>
    <div class="page-hero-label">Boletim CoBraLT</div>
    <h1 class="page-hero-title">Trauma BoleTIME</h1>
    <p class="page-hero-sub">Publicação trimestral do CoBraLT com notícias das ligas, entrevistas, agenda científica, campanhas, projetos e registros da atuação nacional em trauma e emergência.</p>
    <div class="region-stats">
      <div class="region-stat"><div class="region-stat-value"><?= count($editions) ?></div><div class="region-stat-label">edições disponíveis</div></div>
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

<section class="section sponsors-section" aria-labelledby="sponsors-title">
  <div class="section-inner" style="max-width:1120px;">
    <div class="section-header centered" data-animate>
      <div class="divider" style="margin:0 auto 1rem;" aria-hidden="true"></div>
      <span class="section-label">Parcerias que fortalecem o projeto</span>
      <h2 class="section-title" id="sponsors-title">Patrocinadores</h2>
      <p class="section-subtitle">Instituições parceiras que apoiam a produção e a continuidade do Trauma BoleTIME.</p>
    </div>

    <div class="sponsors-grid">
      <a href="https://www.grupomedcof.com.br/?utm_source=google&amp;utm_medium=cpc&amp;utm_campaign=google-cpc-residencia-medica-r-um-ongoing-vendas-residencia-medica&amp;gad_source=1&amp;gad_campaignid=12083919115&amp;gbraid=0AAAAACaXGt1D689RYS1tcRXYz4qOctdNm&amp;gclid=EAIaIQobChMItsiEmdzrlQMV5FZIAB3bsBwWEAAYASAAEgLKJfD_BwE" class="sponsor-card sponsor-card--medcof" target="_blank" rel="noopener noreferrer sponsored" data-animate data-animate-delay="1" aria-label="Visitar o site do Grupo MedCof">
        <div class="sponsor-logo-wrap">
          <img src="../assets/img/patrocinadores/grupo-medcof.png?v=<?= ASSET_VERSION ?>" alt="Logo do Grupo MedCof" loading="lazy">
        </div>
        <div class="sponsor-card-info">
          <h3>Grupo MedCof</h3>
          <span>Conhecer o patrocinador <span aria-hidden="true">↗</span></span>
        </div>
      </a>

      <a href="https://www.csanmek.com.br/" class="sponsor-card sponsor-card--csanmek" target="_blank" rel="noopener noreferrer sponsored" data-animate data-animate-delay="2" aria-label="Visitar o site da CSANMEK Technology">
        <div class="sponsor-logo-wrap">
          <img src="../assets/img/patrocinadores/csanmek-technology.png?v=<?= ASSET_VERSION ?>" alt="Logo da CSANMEK Technology" loading="lazy">
        </div>
        <div class="sponsor-card-info">
          <h3>CSANMEK Technology</h3>
          <span>Conhecer o patrocinador <span aria-hidden="true">↗</span></span>
        </div>
      </a>

      <a href="https://www.idomed.com.br/" class="sponsor-card sponsor-card--idomed" target="_blank" rel="noopener noreferrer sponsored" data-animate data-animate-delay="3" aria-label="Visitar o site do IDOMED">
        <div class="sponsor-logo-wrap">
          <img src="../assets/img/patrocinadores/idomed.png?v=<?= ASSET_VERSION ?>" alt="Logo do IDOMED — Instituto de Educação Médica" loading="lazy">
        </div>
        <div class="sponsor-card-info">
          <h3>IDOMED</h3>
          <span>Instituto de Educação Médica <span aria-hidden="true">↗</span></span>
        </div>
      </a>
    </div>
  </div>
</section>
</main>

<?php layout_footer('../'); ?>
