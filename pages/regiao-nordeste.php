<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/layout.php';

layout_head('Ligas — Nordeste', 'Ligas do Trauma — Nordeste — CoBraLT');
layout_header('ligas');
?>
<nav class="regions-nav" aria-label="Regiões">
  <div class="regions-nav-inner">
    <a href="regiao-norte.php">Norte</a>
    <a href="regiao-nordeste.php" class="active">Nordeste</a>
    <a href="regiao-centro-oeste.php">Centro-Oeste</a>
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
      <span>Nordeste</span>
    </nav>
    <div class="page-hero-label">Ligas filiadas · CoBraLT</div>
    <h1 class="page-hero-title">Nordeste</h1>
    <p class="page-hero-sub">As ligas do Trauma da região Nordeste do Brasil.</p>
    <div class="region-stats">
      <div class="region-stat"><div class="region-stat-value">24</div><div class="region-stat-label">ligas filiadas</div></div>
      <div class="region-stat"><div class="region-stat-value">9</div><div class="region-stat-label">estados</div></div>
    </div>
  </div>
</div>

<section class="section">
  <div class="section-inner">
    <div class="filter-bar" data-animate>
      <label for="filtroEstado">Filtrar por estado:</label>
      <select id="filtroEstado" class="filter-select" onchange="filtrarLigas()">
        <option value="">Todos</option>
        <option value="AL">AL</option>
        <option value="BA">BA</option>
        <option value="CE">CE</option>
        <option value="MA">MA</option>
        <option value="PB">PB</option>
        <option value="PE">PE</option>
        <option value="PI">PI</option>
        <option value="RN">RN</option>
        <option value="SE">SE</option>
      </select>
      <span class="results-count" id="resultsCount"></span>
    </div>
    <div class="ligas-grid" id="ligasGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1rem;margin-top:1.5rem;">
      <div class="liga-card" data-estado="AL"
           data-sigla="LAETE" data-inst="Alagoas"
           data-name="Liga Acadêmica do Estudo do Trauma e Emergência" data-city="Maceió, AL"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAETE</div>
        <div class="liga-card-inst">Alagoas</div>
        <div class="liga-card-name">Liga Acadêmica do Estudo do Trauma e Emergência</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Maceió, AL
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="AL"
           data-sigla="LACAR" data-inst="Alagoas"
           data-name="Liga Acadêmica de Cirurgia de Arapiraca" data-city="Arapiraca, AL"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LACAR</div>
        <div class="liga-card-inst">Alagoas</div>
        <div class="liga-card-name">Liga Acadêmica de Cirurgia de Arapiraca</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Arapiraca, AL
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="AL"
           data-sigla="LAUE-UNCISAL" data-inst="UNCISAL"
           data-name="Liga Acadêmica de Urgência e Emergência" data-city="Maceió, AL"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAUE-UNCISAL</div>
        <div class="liga-card-inst">UNCISAL</div>
        <div class="liga-card-name">Liga Acadêmica de Urgência e Emergência</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Maceió, AL
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="AL"
           data-sigla="LAC-UNCISAL" data-inst="UNCISAL"
           data-name="Liga Alagoana de Cirurgia" data-city="Maceió, AL"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAC-UNCISAL</div>
        <div class="liga-card-inst">UNCISAL</div>
        <div class="liga-card-name">Liga Alagoana de Cirurgia</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Maceió, AL
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="AL"
           data-sigla="LAAP-AL" data-inst="Alagoas"
           data-name="Liga Acadêmica de Atendimento ao Politraumatizado de Alagoas" data-city="Maceió, AL"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAAP-AL</div>
        <div class="liga-card-inst">Alagoas</div>
        <div class="liga-card-name">Liga Acadêmica de Atendimento ao Politraumatizado de Alagoas</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Maceió, AL
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="AL"
           data-sigla="LAMIC" data-inst="Alagoas"
           data-name="Liga Acadêmica de Medicina Interna e Cirúrgica" data-city="Maceió, AL"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAMIC</div>
        <div class="liga-card-inst">Alagoas</div>
        <div class="liga-card-name">Liga Acadêmica de Medicina Interna e Cirúrgica</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Maceió, AL
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="BA"
           data-sigla="LAET-FTC" data-inst="FTC"
           data-name="Liga Acadêmica de Emergência e Trauma" data-city="Feira de Santana, BA"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAET-FTC</div>
        <div class="liga-card-inst">FTC</div>
        <div class="liga-card-name">Liga Acadêmica de Emergência e Trauma</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Feira de Santana, BA
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="BA"
           data-sigla="LBT" data-inst="Bahia"
           data-name="Liga Baiana do Trauma" data-city="Salvador, BA"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LBT</div>
        <div class="liga-card-inst">Bahia</div>
        <div class="liga-card-name">Liga Baiana do Trauma</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Salvador, BA
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="BA"
           data-sigla="LAEME" data-inst="Bahia"
           data-name="Liga Acadêmica do Trauma e Emergências Médicas" data-city="Salvador, BA"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAEME</div>
        <div class="liga-card-inst">Bahia</div>
        <div class="liga-card-name">Liga Acadêmica do Trauma e Emergências Médicas</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Salvador, BA
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="BA"
           data-sigla="LTUESB" data-inst="UESB"
           data-name="Liga de Trauma e Emergência" data-city="Vitória da Conquista, BA"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LTUESB</div>
        <div class="liga-card-inst">UESB</div>
        <div class="liga-card-name">Liga de Trauma e Emergência</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Vitória da Conquista, BA
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="BA"
           data-sigla="LATEC" data-inst="Bahia"
           data-name="Liga de Trauma e Emergências Cirúrgicas" data-city="Salvador, BA"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LATEC</div>
        <div class="liga-card-inst">Bahia</div>
        <div class="liga-card-name">Liga de Trauma e Emergências Cirúrgicas</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Salvador, BA
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="BA"
           data-sigla="LATE-UESC" data-inst="UESC"
           data-name="Liga Acadêmica de Trauma e Emergência" data-city="Ilhéus, BA"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LATE-UESC</div>
        <div class="liga-card-inst">UESC</div>
        <div class="liga-card-name">Liga Acadêmica de Trauma e Emergência</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Ilhéus, BA
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="BA"
           data-sigla="LAEC-EBMSP" data-inst="EBMSP"
           data-name="Liga Acadêmica de Emergências Clínicas" data-city="Salvador, BA"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAEC-EBMSP</div>
        <div class="liga-card-inst">EBMSP</div>
        <div class="liga-card-name">Liga Acadêmica de Emergências Clínicas</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Salvador, BA
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="BA"
           data-sigla="LAORT" data-inst="Bahia"
           data-name="Liga Acadêmica de Ortopedia e Traumatologia" data-city="Salvador, BA"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAORT</div>
        <div class="liga-card-inst">Bahia</div>
        <div class="liga-card-name">Liga Acadêmica de Ortopedia e Traumatologia</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Salvador, BA
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="BA"
           data-sigla="LT-NE" data-inst="Nordeste"
           data-name="Liga do Trauma" data-city="Nordeste, BA"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LT-NE</div>
        <div class="liga-card-inst">Nordeste</div>
        <div class="liga-card-name">Liga do Trauma</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Nordeste, BA
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="BA"
           data-sigla="LAUE-RB" data-inst="Bahia"
           data-name="Liga Acadêmica de Urgência e Emergência do Recôncavo da Bahia" data-city="Santo Antônio de Jesus, BA"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAUE-RB</div>
        <div class="liga-card-inst">Bahia</div>
        <div class="liga-card-name">Liga Acadêmica de Urgência e Emergência do Recôncavo da Bahia</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Santo Antônio de Jesus, BA
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="BA"
           data-sigla="LITRAUMA-NE" data-inst="Nordeste"
           data-name="Liga de Trauma e Medicina Intensiva" data-city="Nordeste, BA"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LITRAUMA-NE</div>
        <div class="liga-card-inst">Nordeste</div>
        <div class="liga-card-name">Liga de Trauma e Medicina Intensiva</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Nordeste, BA
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="CE"
           data-sigla="LDT-CE" data-inst="Ceará"
           data-name="Liga do Trauma do Ceará" data-city="Fortaleza, CE"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LDT-CE</div>
        <div class="liga-card-inst">Ceará</div>
        <div class="liga-card-name">Liga do Trauma do Ceará</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Fortaleza, CE
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MA"
           data-sigla="LATE-MA" data-inst="Maranhão"
           data-name="Liga Acadêmica de Trauma e Emergência do Maranhão" data-city="São Luís, MA"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LATE-MA</div>
        <div class="liga-card-inst">Maranhão</div>
        <div class="liga-card-name">Liga Acadêmica de Trauma e Emergência do Maranhão</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          São Luís, MA
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MA"
           data-sigla="LUTE-UFMA" data-inst="UFMA"
           data-name="Liga de Urgência, Trauma e Emergência" data-city="São Luís, MA"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LUTE-UFMA</div>
        <div class="liga-card-inst">UFMA</div>
        <div class="liga-card-name">Liga de Urgência, Trauma e Emergência</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          São Luís, MA
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="MA"
           data-sigla="LATECA" data-inst="Caxias"
           data-name="Liga de Trauma e Emergência de Caxias" data-city="Caxias, MA"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LATECA</div>
        <div class="liga-card-inst">Caxias</div>
        <div class="liga-card-name">Liga de Trauma e Emergência de Caxias</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Caxias, MA
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="PB"
           data-sigla="LIET" data-inst="Paraíba"
           data-name="Liga de Emergência e Trauma da Paraíba" data-city="João Pessoa, PB"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LIET</div>
        <div class="liga-card-inst">Paraíba</div>
        <div class="liga-card-name">Liga de Emergência e Trauma da Paraíba</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          João Pessoa, PB
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="PB"
           data-sigla="LAUET-PB" data-inst="Paraíba"
           data-name="Liga Acadêmica de Urgência, Emergência e Trauma da Paraíba" data-city="João Pessoa, PB"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAUET-PB</div>
        <div class="liga-card-inst">Paraíba</div>
        <div class="liga-card-name">Liga Acadêmica de Urgência, Emergência e Trauma da Paraíba</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          João Pessoa, PB
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="PB"
           data-sigla="LATECG" data-inst="Campina Grande"
           data-name="Liga Acadêmica de Trauma e Emergência de Campina Grande" data-city="Campina Grande, PB"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LATECG</div>
        <div class="liga-card-inst">Campina Grande</div>
        <div class="liga-card-name">Liga Acadêmica de Trauma e Emergência de Campina Grande</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Campina Grande, PB
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
