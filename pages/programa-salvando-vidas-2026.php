<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/layout.php';

$pdfFile = 'programa-salvando-vidas-2026.pdf';
$pdfDownloadName = 'Edital Programa Salvando Vidas 2026.pdf';
$pdfUrl = '../assets/pdfs/' . rawurlencode($pdfFile);
$pdfOpenUrl = $pdfUrl . '#page=2&view=FitH';
$pdfViewerUrl = $pdfUrl . '#page=2&toolbar=1&navpanes=0&scrollbar=1&view=FitH';

layout_head(
  'Edital Programa Salvando Vidas 2026',
  'Consulte o edital do Programa Salvando Vidas 2026, com resumo, download e visualização online.'
);
layout_header('projetos');
?>

<main id="main-content">

<div class="page-hero">
  <div class="page-hero-inner">
    <nav class="breadcrumb">
      <a href="../index.php">Início</a><span>›</span><a href="projetos.php">Projetos</a><span>›</span><span>Salvando Vidas 2026</span>
    </nav>
    <div class="page-hero-label">Edital oficial</div>
    <h1 class="page-hero-title">Programa Salvando Vidas 2026</h1>
    <p class="page-hero-sub">Documento oficial do ciclo 2026 do Programa Salvando Vidas, iniciativa do CoBraLT voltada à prevenção, primeiros socorros e educação em urgência, emergência e trauma.</p>
    <div class="region-stats">
      <div class="region-stat"><div class="region-stat-value">14</div><div class="region-stat-label">páginas</div></div>
      <div class="region-stat"><div class="region-stat-value">8</div><div class="region-stat-label">meses de ciclo</div></div>
      <div class="region-stat"><div class="region-stat-value">2026</div><div class="region-stat-label">ano vigente</div></div>
    </div>
  </div>
</div>

<section class="section" style="padding-top:3rem;padding-bottom:0;">
  <div class="section-inner" style="max-width:1080px;">
    <div class="programs-feature" data-animate>
      <div class="programs-feature-head">
        <div>
          <span class="section-label">Publicações do projeto</span>
          <h2 class="section-title" style="font-size:1.35rem;margin:0.15rem 0 0;">Materiais Salvando Vidas</h2>
          <p>Conteúdos do ciclo 2026 para leitura online, tela cheia e download.</p>
        </div>
      </div>

      <div class="programs-feature-grid">
        <a href="programa-salvando-vidas-controle-hemorragias.php" class="program-card program-card--salvando" aria-label="Abrir publicação Controle de Hemorragias">
          <div class="program-card-media">
            <img src="../assets/img/programas/salvando-vidas-controle-hemorragias-cover.png" alt="Capa do material Controle de Hemorragias" loading="lazy">
          </div>
          <div class="program-card-body">
            <span class="program-badge">34 páginas · PDF</span>
            <h4>Controle de Hemorragias</h4>
            <p>Material sobre trauma, xABCDE, pressão direta, torniquetes, hemostáticos, Stop the Bleed e prevenção.</p>
            <span class="program-card-link">Ver material <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
          </div>
        </a>

        <a href="programa-salvando-vidas-anafilaxia.php" class="program-card program-card--salvando" aria-label="Abrir publicação Reação Alérgica Grave e Anafilaxia">
          <div class="program-card-media">
            <img src="../assets/img/programas/salvando-vidas-anafilaxia-cover.png" alt="Capa do material Reação Alérgica Grave e Anafilaxia" loading="lazy">
          </div>
          <div class="program-card-body">
            <span class="program-badge">37 páginas · PDF</span>
            <h4>Reação Alérgica Grave e Anafilaxia</h4>
            <p>Material sobre alergias graves, anafilaxia, manifestações clínicas, tratamento, urticária e angioedema.</p>
            <span class="program-card-link">Ver material <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
          </div>
        </a>

        <a href="programa-salvando-vidas-avc-iam.php" class="program-card program-card--salvando" aria-label="Abrir publicação AVC e IAM">
          <div class="program-card-media">
            <img src="../assets/img/programas/salvando-vidas-avc-iam-cover.png" alt="Capa do material AVC e IAM" loading="lazy">
          </div>
          <div class="program-card-body">
            <span class="program-badge">44 páginas · PDF</span>
            <h4>AVC e IAM</h4>
            <p>Material sobre reconhecimento precoce, sinais de alerta e orientações iniciais para AVC e infarto agudo do miocárdio.</p>
            <span class="program-card-link">Ver material <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
          </div>
        </a>

        <a href="programa-salvando-vidas-queimaduras-choques-eletricos.php" class="program-card program-card--salvando" aria-label="Abrir publicação Queimaduras e Choques Elétricos">
          <div class="program-card-media">
            <img src="../assets/img/programas/salvando-vidas-queimaduras-choques-eletricos-cover.png" alt="Capa do material Queimaduras e Choques Elétricos" loading="lazy">
          </div>
          <div class="program-card-body">
            <span class="program-badge">48 páginas · PDF</span>
            <h4>Queimaduras e Choques Elétricos</h4>
            <p>Material sobre prevenção, classificação, cuidados iniciais e segurança em queimaduras e acidentes elétricos.</p>
            <span class="program-card-link">Ver material <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
          </div>
        </a>
      </div>
    </div>
  </div>
</section>

<section class="section" style="padding-top:3rem;">
  <div class="section-inner" style="max-width:1080px;">
    <div class="document-layout">
      <div class="document-summary" data-animate>
        <span class="section-label">Resumo</span>
        <h2 class="section-title">Sobre o edital</h2>
        <p>O edital apresenta o Programa Salvando Vidas (CoBraLT) 2026, projeto social gratuito e sem fins lucrativos realizado com apoio das Ligas Acadêmicas filiadas. A iniciativa busca treinar e conscientizar o público leigo em primeiros socorros, prevenção, urgência, emergência e trauma.</p>
        <p>O ciclo 2026 prevê atividades mensais, ações presenciais, comunicação e marketing, certificação pelo CoBraLT e a Competição da Prevenção, com critérios de pontuação e premiação para as ligas destaque.</p>

        <div class="document-actions" aria-label="Ações do edital">
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
          <li>Adesão destinada a Ligas Acadêmicas filiadas e regularizadas junto ao CoBraLT em 2026.</li>
          <li>Temas do ciclo incluem hemorragias, anafilaxia, queimaduras, AVC/IAM, trauma pediátrico e no idoso, engasgo, RCP, convulsões, enchentes e afogamentos.</li>
          <li>As ações podem ocorrer em locais públicos, canais digitais ou formatos extras desenvolvidos pelas ligas.</li>
          <li>A Competição da Prevenção classifica as ligas por pontuação geral e premia os três maiores destaques.</li>
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
          <h2 class="pdf-viewer-title" id="pdf-viewer-title">Edital Programa Salvando Vidas 2026</h2>
        </div>
        <a href="<?= htmlspecialchars($pdfOpenUrl) ?>" class="btn btn-sm btn-navy" target="_blank" rel="noopener noreferrer">
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M14 3h7v7"/><path d="M10 14 21 3"/><path d="M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5"/></svg>
          Nova guia
        </a>
      </div>
      <object class="pdf-frame" data="<?= htmlspecialchars($pdfViewerUrl) ?>" type="application/pdf" aria-label="Visualizador do Edital Programa Salvando Vidas 2026">
        <div class="pdf-fallback">
          <span class="section-label">Visualização indisponível</span>
          <h3>Não foi possível carregar o PDF nesta área.</h3>
          <p>Abra o edital em uma nova guia ou baixe o arquivo para leitura completa.</p>
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
