<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/layout.php';

layout_head('Ligas — Sudeste MG', 'Ligas do Trauma — Sudeste — Minas Gerais — CoBraLT');
layout_header('ligas');
?>
<nav class="regions-nav" aria-label="Regiões">
  <div class="regions-nav-inner">
    <a href="regiao-norte.php">Norte</a>
    <a href="regiao-nordeste.php">Nordeste</a>
    <a href="regiao-centro-oeste.php">Centro-Oeste</a>
    <a href="regiao-sudeste-mg.php" class="active">Sudeste – MG</a>
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
      <span>Sudeste — Minas Gerais</span>
    </nav>
    <div class="page-hero-label">Ligas filiadas · CoBraLT</div>
    <h1 class="page-hero-title">Sudeste — Minas Gerais</h1>
    <p class="page-hero-sub">Minas Gerais — ligas de trauma filiadas ao CoBraLT.</p>
    <div class="region-stats">
      <div class="region-stat"><div class="region-stat-value">32</div><div class="region-stat-label">ligas filiadas</div></div>
      <div class="region-stat"><div class="region-stat-value">1</div><div class="region-stat-label">estado</div></div>
    </div>
  </div>
</div>

<section class="section">
  <div class="section-inner">
    <div class="filter-bar" data-animate>
      <label for="filtroEstado">Filtrar por estado:</label>
      <select id="filtroEstado" class="filter-select" onchange="filtrarLigas()">
        <option value="">Todos</option>
        <option value="MG">MG</option>
      </select>
      <span class="results-count" id="resultsCount"></span>
    </div>
    <div class="ligas-grid" id="ligasGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1rem;margin-top:1.5rem;">
      <div class="liga-card" data-estado="MG"
           data-sigla="LATE-UFJF/GV" data-inst="UFJF — Gov. Valadares"
           data-name="Liga Acadêmica de Trauma e Emergência de Governador Valadares" data-city="Governador Valadares, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LATE-UFJF/GV</div>
        <div class="liga-card-inst">UFJF — Gov. Valadares</div>
        <div class="liga-card-name">Liga Acadêmica de Trauma e Emergência de Governador Valadares</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Governador Valadares, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LACITE-GV" data-inst="Governador Valadares"
           data-name="Liga Acadêmica de Cirurgia, Trauma e Emergência de Governador Valadares" data-city="Governador Valadares, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LACITE-GV</div>
        <div class="liga-card-inst">Governador Valadares</div>
        <div class="liga-card-name">Liga Acadêmica de Cirurgia, Trauma e Emergência de Governador Valadares</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Governador Valadares, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LACIT-UFU" data-inst="UFU"
           data-name="Liga Acadêmica de Cirurgia e Trauma" data-city="Uberlândia, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LACIT-UFU</div>
        <div class="liga-card-inst">UFU</div>
        <div class="liga-card-name">Liga Acadêmica de Cirurgia e Trauma</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Uberlândia, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LATRE-UNIPAC" data-inst="UNIPAC"
           data-name="Liga Acadêmica do Trauma e Emergência" data-city="Minas Gerais, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LATRE-UNIPAC</div>
        <div class="liga-card-inst">UNIPAC</div>
        <div class="liga-card-name">Liga Acadêmica do Trauma e Emergência</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Minas Gerais, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LT-UFTM" data-inst="UFTM"
           data-name="Liga do Trauma" data-city="Uberaba, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LT-UFTM</div>
        <div class="liga-card-inst">UFTM</div>
        <div class="liga-card-name">Liga do Trauma</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Uberaba, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LATENF-UNIVALE" data-inst="UNIVALE"
           data-name="Liga Acadêmica de Trauma e Emergência em Enfermagem" data-city="Governador Valadares, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LATENF-UNIVALE</div>
        <div class="liga-card-inst">UNIVALE</div>
        <div class="liga-card-name">Liga Acadêmica de Trauma e Emergência em Enfermagem</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Governador Valadares, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LUTE-UFV" data-inst="UFV"
           data-name="Liga Universitária de Trauma, Emergência e Cirurgia" data-city="Viçosa, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LUTE-UFV</div>
        <div class="liga-card-inst">UFV</div>
        <div class="liga-card-name">Liga Universitária de Trauma, Emergência e Cirurgia</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Viçosa, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LITAP" data-inst="Além Paraíba"
           data-name="Liga de Trauma de Além Paraíba" data-city="Além Paraíba, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LITAP</div>
        <div class="liga-card-inst">Além Paraíba</div>
        <div class="liga-card-name">Liga de Trauma de Além Paraíba</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Além Paraíba, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LACIT-PUC MINAS" data-inst="PUC Minas"
           data-name="Liga Acadêmica de Cirurgia e Trauma" data-city="Belo Horizonte, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LACIT-PUC MINAS</div>
        <div class="liga-card-inst">PUC Minas</div>
        <div class="liga-card-name">Liga Acadêmica de Cirurgia e Trauma</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Belo Horizonte, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LAUTE-UFSJ" data-inst="UFSJ"
           data-name="Liga Acadêmica de Urgência, Trauma e Emergência" data-city="São João del-Rei, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAUTE-UFSJ</div>
        <div class="liga-card-inst">UFSJ</div>
        <div class="liga-card-name">Liga Acadêmica de Urgência, Trauma e Emergência</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          São João del-Rei, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LITE-UNIFENAS" data-inst="UNIFENAS"
           data-name="Liga de Trauma e Emergência" data-city="Alfenas, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LITE-UNIFENAS</div>
        <div class="liga-card-inst">UNIFENAS</div>
        <div class="liga-card-name">Liga de Trauma e Emergência</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Alfenas, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LACITRA-FAMINAS BH" data-inst="FAMINAS BH"
           data-name="Liga Acadêmica de Cirurgia e Traumatologia" data-city="Belo Horizonte, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LACITRA-FAMINAS BH</div>
        <div class="liga-card-inst">FAMINAS BH</div>
        <div class="liga-card-name">Liga Acadêmica de Cirurgia e Traumatologia</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Belo Horizonte, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LET-UNIFENAS ALFENAS" data-inst="UNIFENAS Alfenas"
           data-name="Liga de Emergência e Trauma" data-city="Alfenas, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LET-UNIFENAS ALFENAS</div>
        <div class="liga-card-inst">UNIFENAS Alfenas</div>
        <div class="liga-card-name">Liga de Emergência e Trauma</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Alfenas, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LAMURGE-UniBH" data-inst="UniBH"
           data-name="Liga Acadêmica de Medicina de Urgência e Emergência do UniBH" data-city="Belo Horizonte, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAMURGE-UniBH</div>
        <div class="liga-card-inst">UniBH</div>
        <div class="liga-card-name">Liga Acadêmica de Medicina de Urgência e Emergência do UniBH</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Belo Horizonte, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LATEC-UFOP" data-inst="UFOP"
           data-name="Liga Acadêmica do Trauma e Emergências Clínicas" data-city="Ouro Preto, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LATEC-UFOP</div>
        <div class="liga-card-inst">UFOP</div>
        <div class="liga-card-name">Liga Acadêmica do Trauma e Emergências Clínicas</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Ouro Preto, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LAAPH-FCMS" data-inst="FCMS"
           data-name="Liga Acadêmica de Atendimento Pré-Hospitalar" data-city="Minas Gerais, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAAPH-FCMS</div>
        <div class="liga-card-inst">FCMS</div>
        <div class="liga-card-name">Liga Acadêmica de Atendimento Pré-Hospitalar</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Minas Gerais, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LAITEC-FACIG" data-inst="FACIG"
           data-name="Liga Acadêmica Integrada ao Trauma, Emergência e Cirurgia" data-city="Minas Gerais, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAITEC-FACIG</div>
        <div class="liga-card-inst">FACIG</div>
        <div class="liga-card-name">Liga Acadêmica Integrada ao Trauma, Emergência e Cirurgia</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Minas Gerais, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LATE-UFMG" data-inst="UFMG"
           data-name="Liga Acadêmica do Trauma e Emergências Clínicas" data-city="Belo Horizonte, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LATE-UFMG</div>
        <div class="liga-card-inst">UFMG</div>
        <div class="liga-card-name">Liga Acadêmica do Trauma e Emergências Clínicas</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Belo Horizonte, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LATE-UFJF" data-inst="UFJF"
           data-name="Liga Acadêmica do Trauma e Emergência" data-city="Juiz de Fora, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LATE-UFJF</div>
        <div class="liga-card-inst">UFJF</div>
        <div class="liga-card-name">Liga Acadêmica do Trauma e Emergência</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Juiz de Fora, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LATF-Faseh" data-inst="Faseh"
           data-name="Liga Acadêmica do Trauma" data-city="Vespasiano, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LATF-Faseh</div>
        <div class="liga-card-inst">Faseh</div>
        <div class="liga-card-name">Liga Acadêmica do Trauma</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Vespasiano, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LANMUE-FUNORTE" data-inst="FUNORTE"
           data-name="Liga Acadêmica Norte Mineira de Urgência e Emergência" data-city="Montes Claros, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LANMUE-FUNORTE</div>
        <div class="liga-card-inst">FUNORTE</div>
        <div class="liga-card-name">Liga Acadêmica Norte Mineira de Urgência e Emergência</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Montes Claros, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LAUEFA" data-inst="Faculdade Atenas"
           data-name="Liga Acadêmica de Urgência e Emergência da Faculdade Atenas" data-city="Paracatu, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAUEFA</div>
        <div class="liga-card-inst">Faculdade Atenas</div>
        <div class="liga-card-name">Liga Acadêmica de Urgência e Emergência da Faculdade Atenas</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Paracatu, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LAUE-FADIP" data-inst="FADIP"
           data-name="Liga Acadêmica de Urgência e Emergência" data-city="Diamantina, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAUE-FADIP</div>
        <div class="liga-card-inst">FADIP</div>
        <div class="liga-card-name">Liga Acadêmica de Urgência e Emergência</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Diamantina, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LAUEM-UEMG" data-inst="UEMG"
           data-name="Liga Acadêmica de Urgência e Emergência" data-city="Minas Gerais, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAUEM-UEMG</div>
        <div class="liga-card-inst">UEMG</div>
        <div class="liga-card-name">Liga Acadêmica de Urgência e Emergência</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Minas Gerais, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LAT-UNIVAÇO" data-inst="UNIVAÇO"
           data-name="Liga Acadêmica de Trauma" data-city="Ipatinga, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAT-UNIVAÇO</div>
        <div class="liga-card-inst">UNIVAÇO</div>
        <div class="liga-card-name">Liga Acadêmica de Trauma</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Ipatinga, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LAUEM-UFVJM" data-inst="UFVJM"
           data-name="Liga Acadêmica de Urgência e Emergência" data-city="Diamantina, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAUEM-UFVJM</div>
        <div class="liga-card-inst">UFVJM</div>
        <div class="liga-card-name">Liga Acadêmica de Urgência e Emergência</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Diamantina, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LAMU-UFLA" data-inst="UFLA"
           data-name="Liga Acadêmica de Medicina de Urgência e Emergência" data-city="Lavras, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAMU-UFLA</div>
        <div class="liga-card-inst">UFLA</div>
        <div class="liga-card-name">Liga Acadêmica de Medicina de Urgência e Emergência</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Lavras, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LACGT-UNIFAL" data-inst="UNIFAL"
           data-name="Liga Acadêmica de Cirurgia Geral e do Trauma" data-city="Alfenas, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LACGT-UNIFAL</div>
        <div class="liga-card-inst">UNIFAL</div>
        <div class="liga-card-name">Liga Acadêmica de Cirurgia Geral e do Trauma</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Alfenas, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LACCIME" data-inst="Ciências Médicas"
           data-name="Liga Acadêmica de Cirurgia da Ciências Médicas" data-city="Minas Gerais, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LACCIME</div>
        <div class="liga-card-inst">Ciências Médicas</div>
        <div class="liga-card-name">Liga Acadêmica de Cirurgia da Ciências Médicas</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Minas Gerais, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LIRACIRT-UNEC" data-inst="UNEC"
           data-name="Liga Acadêmica de Cirurgia e Traumatologia" data-city="Caratinga, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LIRACIRT-UNEC</div>
        <div class="liga-card-inst">UNEC</div>
        <div class="liga-card-name">Liga Acadêmica de Cirurgia e Traumatologia</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Caratinga, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LAUEM-UIT" data-inst="UIT"
           data-name="Liga Acadêmica de Urgências e Emergências Médicas" data-city="Itabira, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAUEM-UIT</div>
        <div class="liga-card-inst">UIT</div>
        <div class="liga-card-name">Liga Acadêmica de Urgências e Emergências Médicas</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Itabira, MG
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MG"
           data-sigla="LAT-BARBACENA" data-inst="Barbacena"
           data-name="Liga Acadêmica de Trauma" data-city="Barbacena, MG"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAT-BARBACENA</div>
        <div class="liga-card-inst">Barbacena</div>
        <div class="liga-card-name">Liga Acadêmica de Trauma</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Barbacena, MG
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
