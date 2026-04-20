<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/layout.php';

layout_head('Apoiadores', 'Apoiadores do CoBraLT — Comitê Científico e orientadores que apoiam as Ligas do Trauma no Brasil.');
layout_header('apoiadores');
?>
<main id="main-content">

<!-- HERO -->
<div class="page-hero">
  <div class="page-hero-inner">
    <nav class="breadcrumb">
      <a href="../index.php">Início</a><span>›</span><span>Apoiadores</span>
    </nav>
    <div class="page-hero-label">Comitê Científico</div>
    <h1 class="page-hero-title">Nossos Apoiadores</h1>
    <p class="page-hero-sub">Profissionais e instituições que apoiam e orientam as ligas acadêmicas de trauma filiadas ao CoBraLT.</p>
    <div class="region-stats">
      <div class="region-stat">
        <div class="region-stat-value">28</div>
        <div class="region-stat-label">apoiadores</div>
      </div>
      <div class="region-stat">
        <div class="region-stat-value">15+</div>
        <div class="region-stat-label">instituições</div>
      </div>
      <div class="region-stat">
        <div class="region-stat-value">5</div>
        <div class="region-stat-label">regiões do Brasil</div>
      </div>
    </div>
  </div>
</div>

<!-- GRID DE APOIADORES -->
<section class="section" style="padding-top:3rem;" aria-labelledby="apoiadores-grid-title">
  <div class="section-inner" style="max-width:1100px;">

    <div class="section-header centered" data-animate>
      <div class="divider" style="margin:0 auto 1rem;" aria-hidden="true"></div>
      <span class="section-label">Em ordem alfabética</span>
      <h2 class="section-title" id="apoiadores-grid-title">Comitê de Apoiadores</h2>
    </div>

    <div class="supporters-grid">
      <!-- Os cards já têm nome e liga embutidos na imagem -->

      <!-- Amauri Clemente da Rocha -->
            <div class="sup-card" data-animate>
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Amauri Clemente da Rocha em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/amauri-clemente.png" alt="Amauri Clemente da Rocha" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>AC</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

      <!-- Antonio Toshimitsu Onimaru -->
            <div class="sup-card" data-animate data-animate-delay="1">
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Antonio Toshimitsu Onimaru em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/antonio-toshimitsu.png" alt="Antonio Toshimitsu Onimaru" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>AT</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

      <!-- Dr. Caio Duarte -->
            <div class="sup-card" data-animate data-animate-delay="2">
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Dr. Caio Duarte em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/caio-duarte.png" alt="Dr. Caio Duarte" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>CD</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

      <!-- Cesar Vanderlei Carmona -->
            <div class="sup-card" data-animate data-animate-delay="3">
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Cesar Vanderlei Carmona em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/cesar-vanderlei.png" alt="Cesar Vanderlei Carmona" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>CV</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

      <!-- Cláudio Diunky Okawa -->
            <div class="sup-card" data-animate>
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Cláudio Diunky Okawa em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/claudio-diunky.png" alt="Cláudio Diunky Okawa" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>CO</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>  

      <!-- Francisco Eduardo Silva -->
            <div class="sup-card" data-animate data-animate-delay="1">
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Francisco Eduardo Silva em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/francisco-eduardo.png" alt="Francisco Eduardo Silva" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>FE</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

      <!-- Frederico Michelino -->
            <div class="sup-card" data-animate data-animate-delay="2">
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Frederico Michelino em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/frederico-michelino.png" alt="Frederico Michelino" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>FM</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

      <!-- Guilherme Biazotto -->
            <div class="sup-card" data-animate data-animate-delay="3">
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Guilherme Biazotto em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/guilherme-biazotto.png" alt="Guilherme Biazotto" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>GB</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

      <!-- Gustavo de Mendonça Borges -->
            <div class="sup-card" data-animate>
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Gustavo de Mendonça Borges em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/gustavo-mendonca.png" alt="Gustavo de Mendonça Borges" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>GM</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

      <!-- Gustavo P. Fraga -->
            <div class="sup-card" data-animate data-animate-delay="1">
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Gustavo P. Fraga em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/gustavo-fraga.png" alt="Gustavo P. Fraga" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>GF</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

      <!-- Henrique José V. Silveira -->
            <div class="sup-card" data-animate data-animate-delay="2">
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Henrique José V. Silveira em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/henrique-jose.png" alt="Henrique José V. Silveira" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>HJ</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

      <!-- Henrique Valério de Mesquita -->
            <div class="sup-card" data-animate data-animate-delay="3">
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Henrique Valério de Mesquita em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/henrique-valerio.png" alt="Henrique Valério de Mesquita" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>HV</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

      <!-- Jackson Vinícius de Lima Bertuol -->
            <div class="sup-card" data-animate>
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Jackson Vinícius de Lima Bertuol em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/jackson-vinicius.png" alt="Jackson Vinícius de Lima Bertuol" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>JV</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

      <!-- José Alberto Fernandes da Silva Filho -->
            <div class="sup-card" data-animate data-animate-delay="1">
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de José Alberto Fernandes da Silva Filho em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/jose-alberto.png" alt="José Alberto Fernandes da Silva Filho" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>JA</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

      <!-- Larissa Berbert -->
            <div class="sup-card" data-animate data-animate-delay="2">
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Larissa Berbert em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/larissa-berbert.png" alt="Larissa Berbert" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>LB</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

      <!-- Nara Gelle de Oliveira -->
            <div class="sup-card" data-animate data-animate-delay="3">
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Nara Gelle de Oliveira em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/nara-gelle.png" alt="Nara Gelle de Oliveira" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>NG</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

      <!-- Phillipe Abreu -->
            <div class="sup-card" data-animate>
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Phillipe Abreu em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/phillipe-abreu.png" alt="Phillipe Abreu" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>PA</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

      <!-- Dr. Renato Diniz Lins -->
            <div class="sup-card" data-animate data-animate-delay="1">
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Dr. Renato Diniz Lins em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/renato-diniz.png" alt="Dr. Renato Diniz Lins" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>RD</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

      <!-- Rodrigo Barros de Carvalho -->
            <div class="sup-card" data-animate data-animate-delay="2">
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Rodrigo Barros de Carvalho em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/rodrigo-barros.png" alt="Rodrigo Barros de Carvalho" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>RB</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

      <!-- Rodrigo Caselli Belém -->
            <div class="sup-card" data-animate data-animate-delay="3">
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Rodrigo Caselli Belém em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/rodrigo-caselli.png" alt="Rodrigo Caselli Belém" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>RC</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

      <!-- Romeo L. Simões -->
            <div class="sup-card" data-animate>
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Romeo L. Simões em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/romeo-simoes.png" alt="Romeo L. Simões" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>RS</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

      <!-- Prof. Dr. Santiago Servin -->
            <div class="sup-card" data-animate data-animate-delay="1">
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Prof. Dr. Santiago Servin em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/santiago-servin.png" alt="Prof. Dr. Santiago Servin" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>SS</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

      <!-- Saulo Ferreira -->
            <div class="sup-card" data-animate data-animate-delay="1">
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Saulo Ferreira em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/saulo-ferreira.png" alt="Saulo Ferreira" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>SF</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

      <!-- Thiago R. Calderan -->
            <div class="sup-card" data-animate data-animate-delay="2">
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Thiago R. Calderan em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/thiago-calderan.png" alt="Thiago R. Calderan" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>TC</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

      <!-- Tiago Leal Ghezzi -->
            <div class="sup-card" data-animate data-animate-delay="3">
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Tiago Leal Ghezzi em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/tiago-leal.png" alt="Tiago Leal Ghezzi" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>TG</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

      <!-- Vitor F. Kruger -->
            <div class="sup-card" data-animate>
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Vitor F. Kruger em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/vitor-kruger.png" alt="Vitor F. Kruger" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>VK</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

      <!-- Waldemar Prandi Filho -->
            <div class="sup-card" data-animate data-animate-delay="1">
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Waldemar Prandi Filho em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/waldemar-prandi.png" alt="Waldemar Prandi Filho" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>WP</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

      <!-- Wellington José dos Santos -->
            <div class="sup-card" data-animate data-animate-delay="2">
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Wellington José dos Santos em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/wellington-santos.png" alt="Wellington José dos Santos" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>WS</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

      <!-- Willian G. Hashimoto Hilgert de Sousa -->
            <div class="sup-card" data-animate data-animate-delay="3">
        <button class="sup-card-btn" onclick="openLightbox(this)" aria-label="Ver card de Willian G. Hashimoto Hilgert de Sousa em tela cheia">
          <div class="sup-photo-wrap">
            <img src="../assets/img/apoiadores/willian-hashimoto.png" alt="Willian G. Hashimoto Hilgert de Sousa" loading="lazy" onerror="this.closest('.sup-photo-wrap').innerHTML='<div class=sup-avatar>WH</div>'">
            <div class="sup-zoom-hint" aria-hidden="true">
              <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
            </div>
          </div>
        </button>
      </div>

    </div><!-- /supporters-grid -->
  </div>
</section>

<!-- LIGHTBOX -->
<div class="sup-lightbox" id="supLightbox" role="dialog" aria-modal="true" aria-label="Visualizar apoiador">
  <button class="sup-lightbox-close" id="lbClose" aria-label="Fechar">
    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
  </button>
  <button class="sup-lightbox-prev" id="lbPrev" aria-label="Anterior">
    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
  </button>
  <img class="sup-lightbox-img" id="lbImg" src="" alt="">
  <button class="sup-lightbox-next" id="lbNext" aria-label="Próximo">
    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
  </button>
</div>

<script>
(function () {
  'use strict';

  // Collect all card images
  var cards = Array.from(document.querySelectorAll('.sup-card-btn'));
  var current = 0;
  var lb      = document.getElementById('supLightbox');
  var lbImg   = document.getElementById('lbImg');
  var lbClose = document.getElementById('lbClose');
  var lbPrev  = document.getElementById('lbPrev');
  var lbNext  = document.getElementById('lbNext');

  function imgOf(btn) {
    var img = btn.querySelector('img');
    return img ? { src: img.src, alt: img.alt } : { src: '', alt: '' };
  }

  function open(idx) {
    current = (idx + cards.length) % cards.length;
    var info = imgOf(cards[current]);
    lbImg.src = info.src;
    lbImg.alt = info.alt;
    lb.classList.add('open');
    document.body.style.overflow = 'hidden';
    lbClose.focus();
  }

  function close() {
    lb.classList.remove('open');
    document.body.style.overflow = '';
    if (cards[current]) cards[current].focus();
  }

  // Expose globally for onclick attributes in HTML
  window.openLightbox = function (btn) {
    open(cards.indexOf(btn));
  };

  lbClose.addEventListener('click', close);
  lbPrev.addEventListener('click', function () { open(current - 1); });
  lbNext.addEventListener('click', function () { open(current + 1); });

  // Click backdrop to close
  lb.addEventListener('click', function (e) {
    if (e.target === lb) close();
  });

  // Keyboard
  document.addEventListener('keydown', function (e) {
    if (!lb.classList.contains('open')) return;
    if (e.key === 'Escape')     close();
    if (e.key === 'ArrowLeft')  open(current - 1);
    if (e.key === 'ArrowRight') open(current + 1);
  });
})();
</script>

</main>

<?php layout_footer(); ?>
