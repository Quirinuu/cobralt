<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/layout.php';

layout_head('Ligas — Centro-Oeste', 'Ligas do Trauma — Centro-Oeste — CoBraLT');
layout_header('ligas');
?>
<nav class="regions-nav" aria-label="Regiões">
  <div class="regions-nav-inner">
    <a href="regiao-norte.php">Norte</a>
    <a href="regiao-nordeste.php">Nordeste</a>
    <a href="regiao-centro-oeste.php" class="active">Centro-Oeste</a>
    <a href="regiao-sudeste-mg.php">Sudeste – MG</a>
    <a href="regiao-sudeste-sp.php">Sudeste – SP</a>
    <a href="regiao-sudeste-rjes.php">Sudeste – RJ/ES</a>
    <a href="regiao-sul.php">Sul</a>
  </div>
</nav>

<main id="main-content">

<div class="page-hero">
  <div class="page-hero-inner">
    <nav class="breadcrumb">
      <a href="../index.php">Início</a><span>›</span>
      <a href="ligas.php">Ligas filiadas</a><span>›</span>
      <span>Centro-Oeste</span>
    </nav>
    <div class="page-hero-label">Ligas filiadas · CoBraLT</div>
    <h1 class="page-hero-title">Centro-Oeste</h1>
    <p class="page-hero-sub">As ligas do Trauma da região Centro-Oeste do Brasil.</p>
    <div class="region-stats">
      <div class="region-stat"><div class="region-stat-value">27</div><div class="region-stat-label">ligas filiadas</div></div>
      <div class="region-stat"><div class="region-stat-value">4</div><div class="region-stat-label">estados</div></div>
    </div>
  </div>
</div>

<section class="section">
  <div class="section-inner">
    <div class="filter-bar" data-animate>
      <label for="filtroEstado">Filtrar por estado:</label>
      <select id="filtroEstado" class="filter-select" onchange="filtrarLigas()">
        <option value="">Todos</option>
        <option value="DF">DF</option>
        <option value="GO">GO</option>
        <option value="MS">MS</option>
        <option value="MT">MT</option>
      </select>
      <span class="results-count" id="resultsCount"></span>
    </div>
    <div class="ligas-grid" id="ligasGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1rem;margin-top:1.5rem;">
      <div class="liga-card" data-estado="GO"
           data-sigla="LAMEDE" data-inst="Centro-Oeste"
           data-name="Liga Acadêmica de Medicina de Emergência" data-city="Goiânia, GO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAMEDE</div>
        <div class="liga-card-inst">Centro-Oeste</div>
        <div class="liga-card-name">Liga Acadêmica de Medicina de Emergência</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Goiânia, GO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="GO"
           data-sigla="LEMT" data-inst="Centro-Oeste"
           data-name="Liga de Emergências Médicas e Trauma" data-city="Goiânia, GO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LEMT</div>
        <div class="liga-card-inst">Centro-Oeste</div>
        <div class="liga-card-name">Liga de Emergências Médicas e Trauma</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Goiânia, GO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="GO"
           data-sigla="LAUE-CO" data-inst="Centro-Oeste"
           data-name="Liga Acadêmica de Urgência e Emergência Medicina" data-city="Goiânia, GO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAUE-CO</div>
        <div class="liga-card-inst">Centro-Oeste</div>
        <div class="liga-card-name">Liga Acadêmica de Urgência e Emergência Medicina</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Goiânia, GO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="GO"
           data-sigla="LET-CO" data-inst="Centro-Oeste"
           data-name="Liga da Emergência e do Trauma" data-city="Goiânia, GO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LET-CO</div>
        <div class="liga-card-inst">Centro-Oeste</div>
        <div class="liga-card-name">Liga da Emergência e do Trauma</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Goiânia, GO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="GO"
           data-sigla="LAOME" data-inst="Centro-Oeste"
           data-name="Liga Acadêmica de Ortopedia e Medicina do Esporte" data-city="Goiânia, GO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAOME</div>
        <div class="liga-card-inst">Centro-Oeste</div>
        <div class="liga-card-name">Liga Acadêmica de Ortopedia e Medicina do Esporte</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Goiânia, GO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="GO"
           data-sigla="LATEM-CO" data-inst="Centro-Oeste"
           data-name="Liga Acadêmica do Trauma e Emergência Médica" data-city="Goiânia, GO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LATEM-CO</div>
        <div class="liga-card-inst">Centro-Oeste</div>
        <div class="liga-card-name">Liga Acadêmica do Trauma e Emergência Médica</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Goiânia, GO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="GO"
           data-sigla="LACET-CO" data-inst="Centro-Oeste"
           data-name="Liga Acadêmica de Cirurgia e Trauma" data-city="Goiânia, GO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LACET-CO</div>
        <div class="liga-card-inst">Centro-Oeste</div>
        <div class="liga-card-name">Liga Acadêmica de Cirurgia e Trauma</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Goiânia, GO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="GO"
           data-sigla="LAORT-CO" data-inst="Centro-Oeste"
           data-name="Liga Acadêmica de Ortopedia e Traumatologia" data-city="Goiânia, GO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAORT-CO</div>
        <div class="liga-card-inst">Centro-Oeste</div>
        <div class="liga-card-name">Liga Acadêmica de Ortopedia e Traumatologia</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Goiânia, GO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="GO"
           data-sigla="LAAPH-CO" data-inst="Centro-Oeste"
           data-name="Liga Acadêmica de Atendimento Pré-Hospitalar" data-city="Goiânia, GO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAAPH-CO</div>
        <div class="liga-card-inst">Centro-Oeste</div>
        <div class="liga-card-name">Liga Acadêmica de Atendimento Pré-Hospitalar</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Goiânia, GO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="GO"
           data-sigla="LAEM" data-inst="Centro-Oeste"
           data-name="Liga Acadêmica de Emergências Médicas" data-city="Goiânia, GO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAEM</div>
        <div class="liga-card-inst">Centro-Oeste</div>
        <div class="liga-card-name">Liga Acadêmica de Emergências Médicas</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Goiânia, GO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="GO"
           data-sigla="LAOTS" data-inst="Centro-Oeste"
           data-name="Liga Acadêmica de Ortopedia e Traumatologia" data-city="Goiânia, GO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAOTS</div>
        <div class="liga-card-inst">Centro-Oeste</div>
        <div class="liga-card-name">Liga Acadêmica de Ortopedia e Traumatologia</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Goiânia, GO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="GO"
           data-sigla="LAMURGEM-CO" data-inst="Centro-Oeste"
           data-name="Liga Acadêmica de Medicina de Urgência e Emergência" data-city="Goiânia, GO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAMURGEM-CO</div>
        <div class="liga-card-inst">Centro-Oeste</div>
        <div class="liga-card-name">Liga Acadêmica de Medicina de Urgência e Emergência</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Goiânia, GO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="GO"
           data-sigla="LAOT" data-inst="Centro-Oeste"
           data-name="Liga Acadêmica de Ortopedia e Traumatologia" data-city="Goiânia, GO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAOT</div>
        <div class="liga-card-inst">Centro-Oeste</div>
        <div class="liga-card-name">Liga Acadêmica de Ortopedia e Traumatologia</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Goiânia, GO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="GO"
           data-sigla="LITRAUMA-CO" data-inst="Centro-Oeste"
           data-name="Liga Acadêmica do Trauma" data-city="Goiânia, GO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LITRAUMA-CO</div>
        <div class="liga-card-inst">Centro-Oeste</div>
        <div class="liga-card-name">Liga Acadêmica do Trauma</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Goiânia, GO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="GO"
           data-sigla="ORTOPLIGA" data-inst="Centro-Oeste"
           data-name="Liga Acadêmica de Ortopedia" data-city="Goiânia, GO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">ORTOPLIGA</div>
        <div class="liga-card-inst">Centro-Oeste</div>
        <div class="liga-card-name">Liga Acadêmica de Ortopedia</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Goiânia, GO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="GO"
           data-sigla="LATO-CO" data-inst="Centro-Oeste"
           data-name="Liga Acadêmica de Traumatologia e Ortopedia" data-city="Goiânia, GO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LATO-CO</div>
        <div class="liga-card-inst">Centro-Oeste</div>
        <div class="liga-card-name">Liga Acadêmica de Traumatologia e Ortopedia</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Goiânia, GO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="GO"
           data-sigla="LATUE" data-inst="Centro-Oeste"
           data-name="Liga Acadêmica de Trauma, Urgência e Emergência" data-city="Goiânia, GO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LATUE</div>
        <div class="liga-card-inst">Centro-Oeste</div>
        <div class="liga-card-name">Liga Acadêmica de Trauma, Urgência e Emergência</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Goiânia, GO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="GO"
           data-sigla="LATESGO" data-inst="Sudoeste Goiano"
           data-name="Liga Acadêmica de Trauma e Emergência do Sudoeste Goiano" data-city="Rio Verde, GO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LATESGO</div>
        <div class="liga-card-inst">Sudoeste Goiano</div>
        <div class="liga-card-name">Liga Acadêmica de Trauma e Emergência do Sudoeste Goiano</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Rio Verde, GO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="GO"
           data-sigla="LACIT-CO" data-inst="Centro-Oeste"
           data-name="Liga Acadêmica de Cirurgia do Trauma" data-city="Goiânia, GO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LACIT-CO</div>
        <div class="liga-card-inst">Centro-Oeste</div>
        <div class="liga-card-name">Liga Acadêmica de Cirurgia do Trauma</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Goiânia, GO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="GO"
           data-sigla="LTFMUFG" data-inst="UFG"
           data-name="Liga Acadêmica do Trauma da UFG" data-city="Goiânia, GO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LTFMUFG</div>
        <div class="liga-card-inst">UFG</div>
        <div class="liga-card-name">Liga Acadêmica do Trauma da UFG</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Goiânia, GO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="GO"
           data-sigla="LATOMED" data-inst="Centro-Oeste"
           data-name="Liga Acadêmica de Traumato-Ortopedia e Medicina do Esporte" data-city="Goiânia, GO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LATOMED</div>
        <div class="liga-card-inst">Centro-Oeste</div>
        <div class="liga-card-name">Liga Acadêmica de Traumato-Ortopedia e Medicina do Esporte</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Goiânia, GO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="GO"
           data-sigla="LETES" data-inst="Centro-Oeste"
           data-name="Liga Acadêmica de Emergência e Trauma" data-city="Goiânia, GO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LETES</div>
        <div class="liga-card-inst">Centro-Oeste</div>
        <div class="liga-card-name">Liga Acadêmica de Emergência e Trauma</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Goiânia, GO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="GO"
           data-sigla="LUEMU" data-inst="Centro-Oeste"
           data-name="Liga Acadêmica de Urgência e Emergência" data-city="Goiânia, GO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LUEMU</div>
        <div class="liga-card-inst">Centro-Oeste</div>
        <div class="liga-card-name">Liga Acadêmica de Urgência e Emergência</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Goiânia, GO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="GO"
           data-sigla="LEM-CO" data-inst="Centro-Oeste"
           data-name="Liga Acadêmica de Emergência" data-city="Goiânia, GO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LEM-CO</div>
        <div class="liga-card-inst">Centro-Oeste</div>
        <div class="liga-card-name">Liga Acadêmica de Emergência</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Goiânia, GO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="GO"
           data-sigla="LEFT" data-inst="Centro-Oeste"
           data-name="Liga de Emergência e Trauma" data-city="Goiânia, GO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LEFT</div>
        <div class="liga-card-inst">Centro-Oeste</div>
        <div class="liga-card-name">Liga de Emergência e Trauma</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Goiânia, GO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="DF"
           data-sigla="LET-UnB" data-inst="UnB"
           data-name="Liga de Emergência e Trauma da UnB" data-city="Brasília, DF"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LET-UnB</div>
        <div class="liga-card-inst">UnB</div>
        <div class="liga-card-name">Liga de Emergência e Trauma da UnB</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Brasília, DF
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="GO"
           data-sigla="LAMURGEM-FORMOSA" data-inst="Formosa"
           data-name="Liga Acadêmica de Medicina de Urgência e Emergência" data-city="Formosa, GO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAMURGEM-FORMOSA</div>
        <div class="liga-card-inst">Formosa</div>
        <div class="liga-card-name">Liga Acadêmica de Medicina de Urgência e Emergência</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Formosa, GO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="colt-banner" style="padding:3rem 2rem;">
  <div style="max-width:1180px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;gap:2rem;flex-wrap:wrap;">
    <div>
      <h2 class="section-title" style="color:#fff;margin-bottom:.5rem;">Sua liga não está aqui?</h2>
      <p style="color:rgba(255,255,255,.6);font-size:.88rem;">Afilie-se ao CoBraLT e faça parte da maior rede de Ligas do Trauma do Brasil.</p>
    </div>
    <a href="https://docs.google.com/forms/d/e/1FAIpQLSdxlJya6NcxHC1H_Cuao0mtN5_UehL-6RSzuhzj2JlEBtI9qA/viewform"
       target="_blank" rel="noopener noreferrer" class="btn btn-affiliate">Filiar minha liga</a>
  </div>
</section>

</main>

<?php layout_footer(); ?>
