<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/layout.php';

layout_head('Ligas — Sul', 'Ligas do Trauma — Sul — CoBraLT');
layout_header('ligas');
?>
<nav class="regions-nav" aria-label="Regiões">
  <div class="regions-nav-inner">
    <a href="regiao-norte.php">Norte</a>
    <a href="regiao-nordeste.php">Nordeste</a>
    <a href="regiao-centro-oeste.php">Centro-Oeste</a>
    <a href="regiao-sudeste-mg.php">Sudeste – MG</a>
    <a href="regiao-sudeste-sp.php">Sudeste – SP</a>
    <a href="regiao-sudeste-rjes.php">Sudeste – RJ/ES</a>
    <a href="regiao-sul.php" class="active">Sul</a>
  </div>
</nav>

<main id="main-content">

<div class="page-hero">
  <div class="page-hero-inner">
    <nav class="breadcrumb">
      <a href="../index.php">Início</a><span>›</span>
      <a href="ligas.php">Ligas filiadas</a><span>›</span>
      <span>Sul</span>
    </nav>
    <div class="page-hero-label">Ligas filiadas · CoBraLT</div>
    <h1 class="page-hero-title">Sul</h1>
    <p class="page-hero-sub">As ligas do Trauma da região Sul do Brasil.</p>
    <div class="region-stats">
      <div class="region-stat"><div class="region-stat-value">20</div><div class="region-stat-label">ligas filiadas</div></div>
      <div class="region-stat"><div class="region-stat-value">3</div><div class="region-stat-label">estados</div></div>
    </div>
  </div>
</div>

<section class="section">
  <div class="section-inner">
    <div class="filter-bar" data-animate>
      <label for="filtroEstado">Filtrar por estado:</label>
      <select id="filtroEstado" class="filter-select" onchange="filtrarLigas()">
        <option value="">Todos</option>
        <option value="PR">PR</option>
        <option value="RS">RS</option>
        <option value="SC">SC</option>
      </select>
      <span class="results-count" id="resultsCount"></span>
    </div>
    <div class="ligas-grid" id="ligasGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1rem;margin-top:1.5rem;">
      <div class="liga-card" data-estado="RS"
           data-sigla="LAUET-SUL" data-inst="Sul"
           data-name="Liga Acadêmica de Urgência, Emergência e Trauma" data-city="Sul, RS"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAUET-SUL</div>
        <div class="liga-card-inst">Sul</div>
        <div class="liga-card-name">Liga Acadêmica de Urgência, Emergência e Trauma</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Sul, RS
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="RS"
           data-sigla="LIACIT" data-inst="Sul"
           data-name="Liga Acadêmica de Cirurgia e Trauma" data-city="Sul, RS"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LIACIT</div>
        <div class="liga-card-inst">Sul</div>
        <div class="liga-card-name">Liga Acadêmica de Cirurgia e Trauma</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Sul, RS
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="RS"
           data-sigla="LAT-UPF" data-inst="UPF"
           data-name="Liga Acadêmica do Trauma da UPF" data-city="Passo Fundo, RS"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAT-UPF</div>
        <div class="liga-card-inst">UPF</div>
        <div class="liga-card-name">Liga Acadêmica do Trauma da UPF</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Passo Fundo, RS
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="PR"
           data-sigla="LAT-UEL" data-inst="UEL"
           data-name="Liga Acadêmica do Trauma da UEL" data-city="Londrina, PR"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAT-UEL</div>
        <div class="liga-card-inst">UEL</div>
        <div class="liga-card-name">Liga Acadêmica do Trauma da UEL</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Londrina, PR
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="RS"
           data-sigla="LT-PUCRS" data-inst="PUCRS"
           data-name="Liga do Trauma da PUCRS" data-city="Porto Alegre, RS"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LT-PUCRS</div>
        <div class="liga-card-inst">PUCRS</div>
        <div class="liga-card-name">Liga do Trauma da PUCRS</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Porto Alegre, RS
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="RS"
           data-sigla="LT-UNISC" data-inst="UNISC"
           data-name="Liga do Trauma da UNISC" data-city="Santa Cruz do Sul, RS"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LT-UNISC</div>
        <div class="liga-card-inst">UNISC</div>
        <div class="liga-card-name">Liga do Trauma da UNISC</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Santa Cruz do Sul, RS
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="RS"
           data-sigla="LET-UFCSPA" data-inst="UFCSPA"
           data-name="Liga de Emergência e Trauma da UFCSPA" data-city="Porto Alegre, RS"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LET-UFCSPA</div>
        <div class="liga-card-inst">UFCSPA</div>
        <div class="liga-card-name">Liga de Emergência e Trauma da UFCSPA</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Porto Alegre, RS
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="RS"
           data-sigla="LAMET-FEEVALE" data-inst="Feevale"
           data-name="Liga Acadêmica Médica de Emergência e Trauma da Feevale" data-city="Novo Hamburgo, RS"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAMET-FEEVALE</div>
        <div class="liga-card-inst">Feevale</div>
        <div class="liga-card-name">Liga Acadêmica Médica de Emergência e Trauma da Feevale</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Novo Hamburgo, RS
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="RS"
           data-sigla="LTE-UFRGS" data-inst="UFRGS"
           data-name="Liga de Trauma e Emergência UFRGS" data-city="Porto Alegre, RS"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LTE-UFRGS</div>
        <div class="liga-card-inst">UFRGS</div>
        <div class="liga-card-name">Liga de Trauma e Emergência UFRGS</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Porto Alegre, RS
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="RS"
           data-sigla="LAMURGEM-SUL" data-inst="Sul"
           data-name="Liga Acadêmica de Urgência e Emergência" data-city="Porto Alegre, RS"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAMURGEM-SUL</div>
        <div class="liga-card-inst">Sul</div>
        <div class="liga-card-name">Liga Acadêmica de Urgência e Emergência</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Porto Alegre, RS
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="RS"
           data-sigla="LT-IMED" data-inst="IMED"
           data-name="Liga de Trauma da IMED" data-city="Passo Fundo, RS"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LT-IMED</div>
        <div class="liga-card-inst">IMED</div>
        <div class="liga-card-name">Liga de Trauma da IMED</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Passo Fundo, RS
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="RS"
           data-sigla="LAT-UCS" data-inst="UCS"
           data-name="Liga Acadêmica de Trauma da UCS" data-city="Caxias do Sul, RS"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAT-UCS</div>
        <div class="liga-card-inst">UCS</div>
        <div class="liga-card-name">Liga Acadêmica de Trauma da UCS</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Caxias do Sul, RS
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="RS"
           data-sigla="LAT-UNIPAMPA" data-inst="UNIPAMPA"
           data-name="Liga Acadêmica do Trauma" data-city="Bagé, RS"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAT-UNIPAMPA</div>
        <div class="liga-card-inst">UNIPAMPA</div>
        <div class="liga-card-name">Liga Acadêmica do Trauma</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Bagé, RS
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="RS"
           data-sigla="LAEMT-UFPel" data-inst="UFPel"
           data-name="Liga Acadêmica de Emergência Médica e Trauma" data-city="Pelotas, RS"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAEMT-UFPel</div>
        <div class="liga-card-inst">UFPel</div>
        <div class="liga-card-name">Liga Acadêmica de Emergência Médica e Trauma</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Pelotas, RS
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="RS"
           data-sigla="LAUE-UNIVATES" data-inst="UNIVATES"
           data-name="Liga Acadêmica de Urgência e Emergência" data-city="Lajeado, RS"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAUE-UNIVATES</div>
        <div class="liga-card-inst">UNIVATES</div>
        <div class="liga-card-name">Liga Acadêmica de Urgência e Emergência</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Lajeado, RS
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="RS"
           data-sigla="LT-ULBRA" data-inst="ULBRA"
           data-name="Liga do Trauma da ULBRA" data-city="Canoas, RS"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LT-ULBRA</div>
        <div class="liga-card-inst">ULBRA</div>
        <div class="liga-card-name">Liga do Trauma da ULBRA</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Canoas, RS
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="SC"
           data-sigla="LET-UFFS" data-inst="UFFS"
           data-name="Liga de Emergência e Trauma da UFFS" data-city="Chapecó, SC"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LET-UFFS</div>
        <div class="liga-card-inst">UFFS</div>
        <div class="liga-card-name">Liga de Emergência e Trauma da UFFS</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Chapecó, SC
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="RS"
           data-sigla="LAT-FURG" data-inst="FURG"
           data-name="Liga Acadêmica do Trauma da FURG" data-city="Rio Grande, RS"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAT-FURG</div>
        <div class="liga-card-inst">FURG</div>
        <div class="liga-card-name">Liga Acadêmica do Trauma da FURG</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Rio Grande, RS
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="PR"
           data-sigla="LAT-CAJURU" data-inst="Hospital Cajuru"
           data-name="Liga Acadêmica do Trauma Hospital Cajuru" data-city="Curitiba, PR"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAT-CAJURU</div>
        <div class="liga-card-inst">Hospital Cajuru</div>
        <div class="liga-card-name">Liga Acadêmica do Trauma Hospital Cajuru</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Curitiba, PR
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="PR"
           data-sigla="LACT-HUEC" data-inst="HUEC"
           data-name="Liga Acadêmica de Cirurgia do Trauma HUEC" data-city="Curitiba, PR"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LACT-HUEC</div>
        <div class="liga-card-inst">HUEC</div>
        <div class="liga-card-name">Liga Acadêmica de Cirurgia do Trauma HUEC</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Curitiba, PR
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
