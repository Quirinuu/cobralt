<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/layout.php';

$pdfFile = 'programa-prevencao-extensao-junho-laranja-2026.pdf';
$pdfDownloadName = 'Prevencao e Extensao - Junho Laranja 2026.pdf';
$pdfUrl = '../assets/pdfs/' . rawurlencode($pdfFile);
$pdfOpenUrl = $pdfUrl . '#page=2&view=FitH';
$pdfViewerUrl = $pdfUrl . '#page=2&toolbar=1&navpanes=0&scrollbar=1&view=FitH';

layout_head(
  'Prevenção e Extensão - Junho Laranja 2026',
  'Consulte o documento do programa Prevenção e Extensão - Junho Laranja 2026, com resumo, download e visualização online.'
);
layout_header('projetos');
?>

<main id="main-content">

<div class="page-hero">
  <div class="page-hero-inner">
    <nav class="breadcrumb">
      <a href="../index.php">Início</a><span>›</span><a href="projetos.php">Projetos</a><span>›</span><span>Junho Laranja 2026</span>
    </nav>
    <div class="page-hero-label">Prevenção e extensão</div>
    <h1 class="page-hero-title">Junho Laranja 2026</h1>
    <p class="page-hero-sub">Campanha em parceria entre o CoBraLT e a Sociedade Brasileira de Queimaduras para prevenção de queimaduras e acidentes ocupacionais.</p>
    <div class="region-stats">
      <div class="region-stat"><div class="region-stat-value">8</div><div class="region-stat-label">páginas</div></div>
      <div class="region-stat"><div class="region-stat-value">2026</div><div class="region-stat-label">campanha vigente</div></div>
      <div class="region-stat"><div class="region-stat-value">SBQ</div><div class="region-stat-label">parceria institucional</div></div>
    </div>
  </div>
</div>

<section class="section" style="padding-top:3rem;">
  <div class="section-inner" style="max-width:1080px;">
    <div class="document-layout">
      <div class="document-summary" data-animate>
        <span class="section-label">Resumo</span>
        <h2 class="section-title">Sobre a campanha</h2>
        <p>O Junho Laranja 2026 tem como tema "Trabalho Seguro sem Queimaduras" e busca conscientizar a população sobre riscos de queimaduras térmicas, elétricas, químicas e explosões em ambientes de trabalho.</p>
        <p>A proposta mobiliza ligas acadêmicas filiadas em ações educativas e extensionistas, integrando ensino, extensão e impacto social por meio de palestras, capacitações práticas e campanhas visuais permanentes.</p>

        <div class="document-actions" aria-label="Ações do documento">
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
        <span class="section-label">Pontos-chave</span>
        <ul>
          <li>Tema da campanha: "Trabalho Seguro sem Queimaduras".</li>
          <li>Atividades sugeridas incluem palestra de sensibilização, capacitação prática e campanha visual permanente.</li>
          <li>Conteúdos abordam queimaduras térmicas, elétricas e químicas, uso de EPIs, segurança da cena e primeiros socorros.</li>
          <li>Para certificação, as ligas devem enviar relatório com descrição das ações, público alcançado, carga horária, lista de presença e registros fotográficos.</li>
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
          <h2 class="pdf-viewer-title" id="pdf-viewer-title">Prevenção e Extensão - Junho Laranja 2026</h2>
        </div>
        <a href="<?= htmlspecialchars($pdfOpenUrl) ?>" class="btn btn-sm btn-navy" target="_blank" rel="noopener noreferrer">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M14 3h7v7"/><path d="M10 14 21 3"/><path d="M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5"/></svg>
          Nova guia
        </a>
      </div>
      <object class="pdf-frame" data="<?= htmlspecialchars($pdfViewerUrl) ?>" type="application/pdf" aria-label="Visualizador do documento Prevenção e Extensão - Junho Laranja 2026">
        <div class="pdf-fallback">
          <span class="section-label">Visualização indisponível</span>
          <h3>Não foi possível carregar o PDF nesta área.</h3>
          <p>Abra o documento em uma nova guia ou baixe o arquivo para leitura completa.</p>
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
  background: #F97316;
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
