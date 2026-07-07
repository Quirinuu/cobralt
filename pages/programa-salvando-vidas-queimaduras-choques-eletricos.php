<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/layout.php';

$pdfFile = 'programa-salvando-vidas-queimaduras-choques-eletricos.pdf';
$pdfDownloadName = 'Queimaduras e Choques Eletricos - Programa Salvando Vidas.pdf';
$pdfUrl = '../assets/pdfs/' . rawurlencode($pdfFile);
$pdfOpenUrl = $pdfUrl . '#page=1&view=FitH';
$pdfViewerUrl = $pdfUrl . '#page=1&toolbar=1&navpanes=0&scrollbar=1&view=FitH';

layout_head(
  'Salvando Vidas - Queimaduras e Choques Eletricos',
  'Material do Programa Salvando Vidas sobre queimaduras, choques eletricos, prevencao, cuidados iniciais e seguranca.'
);
layout_header('projetos');
?>

<main id="main-content">

<div class="page-hero">
  <div class="page-hero-inner">
    <nav class="breadcrumb">
      <a href="../index.php">Início</a><span>›</span><a href="projetos.php">Projetos</a><span>›</span><a href="programa-salvando-vidas-2026.php">Salvando Vidas 2026</a><span>›</span><span>Queimaduras e Choques Elétricos</span>
    </nav>
    <div class="page-hero-label">Material educativo</div>
    <h1 class="page-hero-title">Queimaduras e Choques Elétricos</h1>
    <p class="page-hero-sub">Publicação do Programa Salvando Vidas 2026 voltada à prevenção, identificação e cuidados iniciais em queimaduras e acidentes elétricos.</p>
    <div class="region-stats">
      <div class="region-stat"><div class="region-stat-value">48</div><div class="region-stat-label">páginas</div></div>
      <div class="region-stat"><div class="region-stat-value">2026</div><div class="region-stat-label">Salvando Vidas</div></div>
      <div class="region-stat"><div class="region-stat-value">PDF</div><div class="region-stat-label">leitura online</div></div>
    </div>
  </div>
</div>

<section class="section" style="padding-top:3rem;">
  <div class="section-inner" style="max-width:1080px;">
    <div class="document-layout">
      <div class="document-summary" data-animate>
        <span class="section-label">Resumo</span>
        <h2 class="section-title">O que este material aborda</h2>
        <p>O documento reúne orientações educativas sobre queimaduras e choques elétricos, abordando prevenção, reconhecimento inicial, classificação e medidas seguras de primeiros cuidados.</p>
        <p>A publicação auxilia as ligas em ações com a comunidade, com foco em evitar agravamentos, reconhecer riscos domésticos e ocupacionais e orientar o acionamento adequado dos serviços de emergência.</p>

        <div class="document-actions" aria-label="Ações do material">
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
          <li>Aborda prevenção de queimaduras térmicas e acidentes elétricos.</li>
          <li>Organiza cuidados iniciais e sinais que exigem atendimento imediato.</li>
          <li>Reforça segurança da cena e condutas adequadas antes do socorro especializado.</li>
          <li>Apoia atividades educativas do ciclo 2026 do Programa Salvando Vidas.</li>
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
          <h2 class="pdf-viewer-title" id="pdf-viewer-title">Queimaduras e Choques Elétricos</h2>
        </div>
        <a href="<?= htmlspecialchars($pdfOpenUrl) ?>" class="btn btn-sm btn-navy" target="_blank" rel="noopener noreferrer">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M14 3h7v7"/><path d="M10 14 21 3"/><path d="M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5"/></svg>
          Nova guia
        </a>
      </div>
      <object class="pdf-frame" data="<?= htmlspecialchars($pdfViewerUrl) ?>" type="application/pdf" aria-label="Visualizador do material Queimaduras e Choques Elétricos">
        <div class="pdf-fallback">
          <span class="section-label">Visualização indisponível</span>
          <h3>Não foi possível carregar o PDF nesta área.</h3>
          <p>Abra o material em uma nova guia ou baixe o arquivo para leitura completa.</p>
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
.document-layout{display:grid;grid-template-columns:minmax(0,1.35fr) minmax(280px,.85fr);gap:1.5rem;align-items:stretch}.document-summary,.document-highlights,.pdf-viewer-shell{background:var(--white);border:1px solid var(--slate-200);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm)}.document-summary{padding:1.75rem}.document-summary p{color:var(--slate-600);font-size:.9rem;line-height:1.8;margin-bottom:.9rem}.document-actions{display:flex;gap:.75rem;flex-wrap:wrap;margin-top:1.25rem}.document-highlights{padding:1.5rem}.document-highlights ul{display:flex;flex-direction:column;gap:.85rem;list-style:none;padding:0;margin:.35rem 0 0}.document-highlights li{position:relative;padding-left:1rem;color:var(--slate-600);font-size:.82rem;line-height:1.65}.document-highlights li::before{content:'';position:absolute;left:0;top:.72em;width:5px;height:5px;border-radius:50%;background:var(--sky)}.pdf-section{padding-top:1rem;background:linear-gradient(180deg,var(--off-white) 0%,#fff 100%)}.pdf-viewer-shell{overflow:hidden}.pdf-viewer-header{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem 1.25rem;border-bottom:1px solid var(--slate-200);background:#fff}.pdf-viewer-title{margin:0;color:var(--navy);font-family:var(--font-display);font-size:1rem;font-weight:800;line-height:1.25}.pdf-frame{display:block;width:100%;height:min(78vh,860px);min-height:620px;border:0;background:var(--slate-100)}@media (max-width:900px){.document-layout{grid-template-columns:1fr}}@media (max-width:600px){.document-summary,.document-highlights{padding:1.25rem}.document-actions .btn,.pdf-viewer-header .btn{width:100%;justify-content:center}.pdf-viewer-header{align-items:flex-start;flex-direction:column}.pdf-frame{height:70vh;min-height:480px}}
</style>

</main>

<?php layout_footer('../'); ?>
