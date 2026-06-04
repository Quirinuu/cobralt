<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/layout.php';

$pdfFile = 'trauma-boletime-maio-2026.pdf';
$pdfDownloadName = 'Trauma BoleTIME - Maio 2026.pdf';
$pdfUrl = '../assets/pdfs/' . rawurlencode($pdfFile);
$pdfOpenUrl = $pdfUrl . '#page=1&view=FitH';
$pdfViewerUrl = $pdfUrl . '#page=1&toolbar=1&navpanes=0&scrollbar=1&view=FitH';

layout_head(
  'Trauma BoleTIME - Maio 2026',
  'Edição inaugural do Trauma BoleTIME, boletim trimestral do CoBraLT.'
);
layout_header('boletimes');
?>

<main id="main-content">

<div class="page-hero">
  <div class="page-hero-inner">
    <nav class="breadcrumb">
      <a href="../index.php">Início</a><span>›</span><a href="boletimes.php">Trauma BoleTIME</a><span>›</span><span>Maio 2026</span>
    </nav>
    <div class="page-hero-label">Edição 1 · 2º trimestre/2026</div>
    <h1 class="page-hero-title">Trauma BoleTIME - Maio 2026</h1>
    <p class="page-hero-sub">Edição inaugural do informativo trimestral do CoBraLT, reunindo notícias institucionais, entrevistas, ações das ligas, eventos científicos e agenda da área do trauma.</p>
    <div class="region-stats">
      <div class="region-stat"><div class="region-stat-value">12</div><div class="region-stat-label">páginas</div></div>
      <div class="region-stat"><div class="region-stat-value">1ª</div><div class="region-stat-label">edição</div></div>
      <div class="region-stat"><div class="region-stat-value">2026</div><div class="region-stat-label">2º trimestre</div></div>
    </div>
  </div>
</div>

<section class="section" style="padding-top:3rem;">
  <div class="section-inner" style="max-width:1080px;">
    <div class="document-layout">
      <div class="document-summary" data-animate>
        <span class="section-label">Resumo da edição</span>
        <h2 class="section-title">O que esta edição traz</h2>
        <p>A primeira edição do Trauma BoleTIME apresenta o momento de formalização e fortalecimento institucional do CoBraLT, celebrando 23 anos de história e mais de 180 ligas filiadas no Brasil.</p>
        <p>O boletim reúne entrevista com o Prof. Dr. Gustavo Pereira Fraga, reportagens sobre ações das ligas, treinamento internacional para múltiplas vítimas, participação de diretores em fóruns do CFM, destaque das ligas brasileiras no ECTES/WTC e agenda de eventos científicos.</p>

        <div class="document-actions" aria-label="Ações da edição">
          <a href="<?= htmlspecialchars($pdfOpenUrl) ?>" class="btn btn-primary" target="_blank" rel="noopener noreferrer">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M14 3h7v7"/><path d="M10 14 21 3"/><path d="M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5"/></svg>
            Abrir em tela cheia
          </a>
          <a href="<?= htmlspecialchars($pdfUrl) ?>" class="btn btn-navy" download="<?= htmlspecialchars($pdfDownloadName) ?>">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/></svg>
            Baixar PDF
          </a>
        </div>
      </div>

      <div class="document-highlights" data-animate data-animate-delay="1">
        <span class="section-label">Destaques</span>
        <ul>
          <li>CoBraLT chega aos 23 anos com atuação nacional e mais de 180 ligas filiadas.</li>
          <li>Entrevista com o Prof. Dr. Gustavo Pereira Fraga sobre o BoleTIME e a Cirurgia do Trauma no Brasil.</li>
          <li>Reportagens sobre Maio Amarelo, MCM, 28º CoLT + II COTREM, ECTES/WTC e fóruns no CFM.</li>
          <li>Agenda com congressos, reuniões mensais e eventos nacionais e internacionais de trauma.</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<section class="section pdf-section" aria-labelledby="pdf-viewer-title">
  <div class="section-inner" style="max-width:1180px;">
    <div class="pdf-viewer-shell" data-animate>
      <div class="pdf-viewer-header">
        <div>
          <span class="section-label">Visualização online</span>
          <h2 class="pdf-viewer-title" id="pdf-viewer-title">Trauma BoleTIME - Maio 2026</h2>
        </div>
        <a href="<?= htmlspecialchars($pdfOpenUrl) ?>" class="btn btn-sm btn-navy" target="_blank" rel="noopener noreferrer">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M14 3h7v7"/><path d="M10 14 21 3"/><path d="M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5"/></svg>
          Nova guia
        </a>
      </div>
      <object class="pdf-frame" data="<?= htmlspecialchars($pdfViewerUrl) ?>" type="application/pdf" aria-label="Visualizador do Trauma BoleTIME - Maio 2026">
        <div class="pdf-fallback">
          <span class="section-label">Visualização indisponível</span>
          <h3>Não foi possível carregar o PDF nesta área.</h3>
          <p>Abra a edição em uma nova guia ou baixe o arquivo para leitura completa.</p>
          <div class="pdf-fallback-actions">
            <a href="<?= htmlspecialchars($pdfOpenUrl) ?>" class="btn btn-primary" target="_blank" rel="noopener noreferrer">Abrir PDF</a>
            <a href="<?= htmlspecialchars($pdfUrl) ?>" class="btn btn-navy" download="<?= htmlspecialchars($pdfDownloadName) ?>">Baixar PDF</a>
          </div>
        </div>
      </object>
    </div>
  </div>
</section>

<style>
.document-layout {
  display: grid;
  grid-template-columns: minmax(0, 1.35fr) minmax(280px, 0.85fr);
  gap: 1.5rem;
  align-items: stretch;
}
.document-summary,
.document-highlights,
.pdf-viewer-shell {
  background: var(--white);
  border: 1px solid var(--slate-200);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-sm);
}
.document-summary {
  padding: 1.75rem;
}
.document-summary p {
  color: var(--slate-600);
  font-size: 0.9rem;
  line-height: 1.8;
  margin-bottom: 0.9rem;
}
.document-actions {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
  margin-top: 1.25rem;
}
.document-highlights {
  padding: 1.5rem;
}
.document-highlights ul {
  display: flex;
  flex-direction: column;
  gap: 0.85rem;
  list-style: none;
  padding: 0;
  margin: 0.35rem 0 0;
}
.document-highlights li {
  position: relative;
  padding-left: 1rem;
  color: var(--slate-600);
  font-size: 0.82rem;
  line-height: 1.65;
}
.document-highlights li::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0.72em;
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: var(--sky);
}
.pdf-section {
  padding-top: 1rem;
  background: linear-gradient(180deg, var(--off-white) 0%, #fff 100%);
}
.pdf-viewer-shell {
  overflow: hidden;
}
.pdf-viewer-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 1rem 1.25rem;
  border-bottom: 1px solid var(--slate-200);
  background: #fff;
}
.pdf-viewer-title {
  margin: 0;
  color: var(--navy);
  font-family: var(--font-display);
  font-size: 1rem;
  font-weight: 800;
  line-height: 1.25;
}
.pdf-frame {
  display: block;
  width: 100%;
  height: min(78vh, 860px);
  min-height: 620px;
  border: 0;
  background: var(--slate-100);
}
@media (max-width: 900px) {
  .document-layout {
    grid-template-columns: 1fr;
  }
}
@media (max-width: 600px) {
  .document-summary,
  .document-highlights {
    padding: 1.25rem;
  }
  .document-actions .btn {
    width: 100%;
    justify-content: center;
  }
  .pdf-viewer-header {
    align-items: flex-start;
    flex-direction: column;
  }
  .pdf-viewer-header .btn {
    width: 100%;
    justify-content: center;
  }
  .pdf-frame {
    height: 70vh;
    min-height: 480px;
  }
}
</style>

</main>

<?php layout_footer('../'); ?>
