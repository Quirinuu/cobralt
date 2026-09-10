<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/layout.php';

layout_head('Prevenção de Trauma', 'Materiais do CoBraLT para prevenção de trauma em pessoas idosas, crianças e adolescentes.');
layout_header('projetos');
?>
<main id="main-content">
  <div class="page-hero prevention-hub-hero">
    <div class="page-hero-inner">
      <nav class="breadcrumb" aria-label="Navegação estrutural"><a href="../index">Início</a><span>›</span><a href="projetos">Projetos</a><span>›</span><span>Prevenção de Trauma</span></nav>
      <div class="page-hero-label">Projeto CoBraLT · 2026</div>
      <h1 class="page-hero-title">Prevenção de Trauma</h1>
      <p class="page-hero-sub">Materiais educativos para reconhecer riscos, promover ambientes mais seguros e fortalecer ações de prevenção em diferentes fases da vida.</p>
      <div class="region-stats">
        <div class="region-stat"><div class="region-stat-value">2</div><div class="region-stat-label">publicações</div></div>
        <div class="region-stat"><div class="region-stat-value">99</div><div class="region-stat-label">páginas de conteúdo</div></div>
        <div class="region-stat"><div class="region-stat-value">2026</div><div class="region-stat-label">edição atual</div></div>
      </div>
    </div>
  </div>

  <section class="section" style="padding-top:3rem;">
    <div class="section-inner">
      <div class="section-header centered" data-animate>
        <span class="section-label">Escolha o material</span>
        <h2 class="section-title">Prevenção em cada fase da vida</h2>
        <p class="section-subtitle">Acesse a publicação completa, leia online ou baixe o PDF para atividades educativas e extensionistas.</p>
      </div>
      <div class="prevention-materials-grid">
        <a href="prevencao-trauma-idoso" class="prevention-material-card" data-animate aria-label="Abrir Prevenção de Trauma no Idoso">
          <div class="prevention-material-media"><img src="../assets/img/programas/prevencao-trauma-idoso-2026-cover.webp" alt="Capa Prevenção de Trauma no Idoso" loading="lazy"></div>
          <div class="prevention-material-body"><span class="program-badge">53 páginas · PDF</span><h3>Prevenção de Trauma no Idoso</h3><p>Quedas, trânsito, segurança no domicílio, envelhecimento saudável e preservação da autonomia.</p><span class="program-card-link">Acessar material →</span></div>
        </a>
        <a href="prevencao-trauma-pediatrico" class="prevention-material-card" data-animate data-animate-delay="1" aria-label="Abrir Prevenção de Trauma Pediátrico">
          <div class="prevention-material-media"><img src="../assets/img/programas/prevencao-trauma-pediatrico-2026-cover.webp" alt="Capa Prevenção de Trauma Pediátrico" loading="lazy"></div>
          <div class="prevention-material-body"><span class="program-badge">46 páginas · PDF</span><h3>Prevenção de Trauma Pediátrico</h3><p>Segurança no trânsito, prevenção em casa, primeiros socorros e proteção de crianças e adolescentes.</p><span class="program-card-link">Acessar material →</span></div>
        </a>
      </div>
    </div>
  </section>
</main>
<style>
.prevention-hub-hero{background:linear-gradient(135deg,#002B4E 0%,#013684 58%,#991B1B 100%)}
.prevention-materials-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1.5rem;max-width:960px;margin:2rem auto 0}
.prevention-material-card{display:grid;grid-template-columns:210px minmax(0,1fr);min-height:330px;background:#fff;border:1px solid var(--slate-200);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm);overflow:hidden;color:inherit;text-decoration:none;transition:transform var(--transition),box-shadow var(--transition),border-color var(--transition)}
.prevention-material-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-lg);border-color:#B91C1C}
.prevention-material-media{overflow:hidden;background:var(--slate-100)}
.prevention-material-media img{width:100%;height:100%;object-fit:cover;object-position:top center;display:block}
.prevention-material-body{padding:1.5rem;display:flex;flex-direction:column;justify-content:center}
.prevention-material-body h3{font-family:var(--font-display);font-size:1.2rem;color:var(--navy);line-height:1.25;margin:.8rem 0 .65rem}
.prevention-material-body p{color:var(--slate-600);font-size:.84rem;line-height:1.7;margin:0 0 1rem}
.prevention-material-body .program-card-link{margin-top:auto}
@media(max-width:900px){.prevention-materials-grid{grid-template-columns:1fr}.prevention-material-card{grid-template-columns:180px minmax(0,1fr)}}
@media(max-width:560px){.prevention-material-card{grid-template-columns:1fr}.prevention-material-media{height:330px}.prevention-material-body{padding:1.25rem}}
</style>
<?php layout_footer('../'); ?>
