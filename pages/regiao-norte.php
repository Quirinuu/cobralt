<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/layout.php';

layout_head('Ligas — Norte', 'Ligas do Trauma — Norte — CoBraLT');
layout_header('ligas');
?>
<nav class="regions-nav" aria-label="Regiões">
  <div class="regions-nav-inner">
    <a href="regiao-norte.php" class="active">Norte</a>
    <a href="regiao-nordeste.php">Nordeste</a>
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
      <span>Norte</span>
    </nav>
    <div class="page-hero-label">Ligas filiadas · CoBraLT</div>
    <h1 class="page-hero-title">Norte</h1>
    <p class="page-hero-sub">As ligas do Trauma da região Norte do Brasil.</p>
    <div class="region-stats">
      <div class="region-stat"><div class="region-stat-value">23</div><div class="region-stat-label">ligas filiadas</div></div>
      <div class="region-stat"><div class="region-stat-value">7</div><div class="region-stat-label">estados</div></div>
    </div>
  </div>
</div>

<section class="section">
  <div class="section-inner">
    <div class="filter-bar" data-animate>
      <label for="filtroEstado">Filtrar por estado:</label>
      <select id="filtroEstado" class="filter-select" onchange="filtrarLigas()">
        <option value="">Todos</option>
        <option value="AC">AC</option>
        <option value="AM">AM</option>
        <option value="AP">AP</option>
        <option value="PA">PA</option>
        <option value="RO">RO</option>
        <option value="RR">RR</option>
        <option value="TO">TO</option>
      </select>
      <span class="results-count" id="resultsCount"></span>
    </div>
    <div class="ligas-grid" id="ligasGrid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1rem;margin-top:1.5rem;">
      <div class="liga-card" data-estado="RO"
           data-sigla="LACCRO" data-inst="Rondônia"
           data-name="Liga Acadêmica de Clínica Cirúrgica de Rondônia" data-city="Porto Velho, RO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LACCRO</div>
        <div class="liga-card-inst">Rondônia</div>
        <div class="liga-card-name">Liga Acadêmica de Clínica Cirúrgica de Rondônia</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Porto Velho, RO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="TO"
           data-sigla="LUTTE-UFT" data-inst="UFT"
           data-name="Liga Universitária Tocantinense de Trauma e Emergência" data-city="Palmas, TO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LUTTE-UFT</div>
        <div class="liga-card-inst">UFT</div>
        <div class="liga-card-name">Liga Universitária Tocantinense de Trauma e Emergência</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Palmas, TO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="TO"
           data-sigla="LCT-ITPAC" data-inst="ITPAC"
           data-name="Liga de Cirurgia do Trauma do ITPAC" data-city="Araguaína, TO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LCT-ITPAC</div>
        <div class="liga-card-inst">ITPAC</div>
        <div class="liga-card-name">Liga de Cirurgia do Trauma do ITPAC</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Araguaína, TO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="AM"
           data-sigla="LAT" data-inst="Amazonas"
           data-name="Liga Amazonense do Trauma" data-city="Manaus, AM"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAT</div>
        <div class="liga-card-inst">Amazonas</div>
        <div class="liga-card-name">Liga Amazonense do Trauma</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Manaus, AM
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="AC"
           data-sigla="ALFA-RB" data-inst="Rio Branco"
           data-name="Projeto Alfa Rio Branco" data-city="Rio Branco, AC"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">ALFA-RB</div>
        <div class="liga-card-inst">Rio Branco</div>
        <div class="liga-card-name">Projeto Alfa Rio Branco</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Rio Branco, AC
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="AM"
           data-sigla="ALFA-MN" data-inst="Manaus"
           data-name="Projeto Alfa Manaus" data-city="Manaus, AM"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">ALFA-MN</div>
        <div class="liga-card-inst">Manaus</div>
        <div class="liga-card-name">Projeto Alfa Manaus</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Manaus, AM
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="TO"
           data-sigla="LAT-ARA" data-inst="Araguaína"
           data-name="Liga Araguainense do Trauma" data-city="Araguaína, TO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAT-ARA</div>
        <div class="liga-card-inst">Araguaína</div>
        <div class="liga-card-name">Liga Araguainense do Trauma</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Araguaína, TO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="TO"
           data-sigla="LATE-ARA" data-inst="Araguaína"
           data-name="Liga Araguainense do Trauma de Enfermagem" data-city="Araguaína, TO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LATE-ARA</div>
        <div class="liga-card-inst">Araguaína</div>
        <div class="liga-card-name">Liga Araguainense do Trauma de Enfermagem</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Araguaína, TO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="PA"
           data-sigla="LPT" data-inst="Pará"
           data-name="Liga Paraense do Trauma" data-city="Belém, PA"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LPT</div>
        <div class="liga-card-inst">Pará</div>
        <div class="liga-card-name">Liga Paraense do Trauma</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Belém, PA
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="AM"
           data-sigla="LATEENF-UEA" data-inst="UEA"
           data-name="Liga do Trauma e Emergência de Enfermagem da UEA" data-city="Manaus, AM"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LATEENF-UEA</div>
        <div class="liga-card-inst">UEA</div>
        <div class="liga-card-name">Liga do Trauma e Emergência de Enfermagem da UEA</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Manaus, AM
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="RR"
           data-sigla="LTRR" data-inst="Roraima"
           data-name="Liga do Trauma de Roraima" data-city="Boa Vista, RR"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LTRR</div>
        <div class="liga-card-inst">Roraima</div>
        <div class="liga-card-name">Liga do Trauma de Roraima</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Boa Vista, RR
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="TO"
           data-sigla="LAIT-UFT" data-inst="UFT"
           data-name="Liga Acadêmica Intensiva do Trauma da UFT" data-city="Palmas, TO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAIT-UFT</div>
        <div class="liga-card-inst">UFT</div>
        <div class="liga-card-name">Liga Acadêmica Intensiva do Trauma da UFT</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Palmas, TO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="AM"
           data-sigla="LAIRT" data-inst="Norte"
           data-name="Liga de Atendimento Integrado e Reabilitação ao Trauma" data-city="Norte, AM"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAIRT</div>
        <div class="liga-card-inst">Norte</div>
        <div class="liga-card-name">Liga de Atendimento Integrado e Reabilitação ao Trauma</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Norte, AM
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="AM"
           data-sigla="LAUEEN" data-inst="Norte"
           data-name="Liga Acadêmica de Urgência e Emergência em Enfermagem" data-city="Norte, AM"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAUEEN</div>
        <div class="liga-card-inst">Norte</div>
        <div class="liga-card-name">Liga Acadêmica de Urgência e Emergência em Enfermagem</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Norte, AM
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="RO"
           data-sigla="LACITRE-RO" data-inst="Rondônia"
           data-name="Liga Acadêmica de Cirurgia, Trauma e Emergência de Rondônia" data-city="Porto Velho, RO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LACITRE-RO</div>
        <div class="liga-card-inst">Rondônia</div>
        <div class="liga-card-name">Liga Acadêmica de Cirurgia, Trauma e Emergência de Rondônia</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Porto Velho, RO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="TO"
           data-sigla="LAUM-UNIRG" data-inst="Unirg"
           data-name="Liga Acadêmica de Urgências Médicas da Unirg" data-city="Gurupi, TO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAUM-UNIRG</div>
        <div class="liga-card-inst">Unirg</div>
        <div class="liga-card-name">Liga Acadêmica de Urgências Médicas da Unirg</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Gurupi, TO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="TO"
           data-sigla="LAMUEM-UNIRG" data-inst="Unirg"
           data-name="Liga Acadêmica de Medicina de Urgência e Emergência da Unirg" data-city="Gurupi, TO"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAMUEM-UNIRG</div>
        <div class="liga-card-inst">Unirg</div>
        <div class="liga-card-name">Liga Acadêmica de Medicina de Urgência e Emergência da Unirg</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Gurupi, TO
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="AM"
           data-sigla="LAMEU" data-inst="Norte"
           data-name="Liga Acadêmica de Medicina de Emergência e Urgência" data-city="Norte, AM"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAMEU</div>
        <div class="liga-card-inst">Norte</div>
        <div class="liga-card-name">Liga Acadêmica de Medicina de Emergência e Urgência</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Norte, AM
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="RR"
           data-sigla="LTFRR" data-inst="Roraima"
           data-name="Liga do Trauma de Fisioterapia de Roraima" data-city="Boa Vista, RR"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LTFRR</div>
        <div class="liga-card-inst">Roraima</div>
        <div class="liga-card-name">Liga do Trauma de Fisioterapia de Roraima</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Boa Vista, RR
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="AP"
           data-sigla="LAUEM-AP" data-inst="Amapá"
           data-name="Liga Acadêmica de Urgência e Emergência do Amapá" data-city="Macapá, AP"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAUEM-AP</div>
        <div class="liga-card-inst">Amapá</div>
        <div class="liga-card-name">Liga Acadêmica de Urgência e Emergência do Amapá</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Macapá, AP
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="AM"
           data-sigla="LACASH" data-inst="Norte"
           data-name="Liga Acadêmica de Cuidados Avançados em Saúde" data-city="Norte, AM"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LACASH</div>
        <div class="liga-card-inst">Norte</div>
        <div class="liga-card-name">Liga Acadêmica de Cuidados Avançados em Saúde</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Norte, AM
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="AM"
           data-sigla="LOT-ULBRA" data-inst="ULBRA"
           data-name="Liga de Ortopedia e Traumatologia" data-city="Norte, AM"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LOT-ULBRA</div>
        <div class="liga-card-inst">ULBRA</div>
        <div class="liga-card-name">Liga de Ortopedia e Traumatologia</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Norte, AM
        </div>
        <div class="liga-card-footer">
          <span class="liga-card-since">Liga filiada</span>
          <span class="liga-card-arrow">Ver detalhes <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></span>
        </div>
      </div>
      <div class="liga-card" data-estado="AM"
           data-sigla="LAEC" data-inst="Norte"
           data-name="Liga Acadêmica de Emergências Clínicas" data-city="Norte, AM"
           onclick="openModal(this)" style="cursor:pointer;">
        <div class="liga-card-emoji">🏥</div>
        <div class="liga-card-sigla">LAEC</div>
        <div class="liga-card-inst">Norte</div>
        <div class="liga-card-name">Liga Acadêmica de Emergências Clínicas</div>
        <div class="liga-card-city">
          <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="13" height="13"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
          Norte, AM
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
