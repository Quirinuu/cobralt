<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/layout.php';

$documents = [
  [
    'key' => 'edital',
    'label' => 'Edital de adesão',
    'title' => 'Edital de Adesão e Implementação do Programa P.A.R.T.Y. Brasil',
    'file' => 'programa-party-brasil-2026-edital.pdf',
    'download' => 'Programa PARTY Brasil 2026 - Edital.pdf',
    'pages' => '13 páginas',
    'summary' => 'Documento com as diretrizes para adesão, implementação, funcionamento, monitoramento e certificação dos núcleos locais do Programa P.A.R.T.Y. Brasil.',
  ],
  [
    'key' => 'ficha',
    'label' => 'Ficha de inscrição',
    'title' => 'Ficha de Adesão – Programa P.A.R.T.Y. Brasil',
    'file' => 'programa-party-brasil-2026-ficha.pdf',
    'download' => 'Programa PARTY Brasil 2026 - Ficha de Adesao.pdf',
    'pages' => '2 páginas',
    'summary' => 'Formulário para envio dos dados institucionais, coordenação, equipe executora, estrutura, parcerias, justificativa e plano de implementação da liga.',
  ],
];

foreach ($documents as &$doc) {
  $doc['url'] = '../assets/pdfs/' . rawurlencode($doc['file']);
  $doc['open_url'] = $doc['url'] . '#page=1&view=FitH';
  $doc['viewer_url'] = $doc['url'] . '#page=1&toolbar=1&navpanes=0&scrollbar=1&view=FitH';
}
unset($doc);

layout_head(
  'Programa P.A.R.T.Y. Brasil 2026',
  'Consulte o edital e a ficha de adesão do Programa P.A.R.T.Y. Brasil 2026.'
);
layout_header('projetos');
?>

<main id="main-content">

<div class="page-hero">
  <div class="page-hero-inner">
    <nav class="breadcrumb">
      <a href="../index.php">Início</a><span>›</span><a href="projetos.php">Projetos</a><span>›</span><span>P.A.R.T.Y. Brasil 2026</span>
    </nav>
    <div class="page-hero-label">Programa de prevenção</div>
    <h1 class="page-hero-title">Programa P.A.R.T.Y. Brasil 2026</h1>
    <p class="page-hero-sub">Edital e ficha de adesão para implementação do Programa P.A.R.T.Y. Brasil pelas Ligas Acadêmicas de Trauma filiadas ao CoBraLT.</p>
    <div class="region-stats">
      <div class="region-stat"><div class="region-stat-value">2</div><div class="region-stat-label">documentos</div></div>
      <div class="region-stat"><div class="region-stat-value">1986</div><div class="region-stat-label">origem do programa</div></div>
      <div class="region-stat"><div class="region-stat-value">2026</div><div class="region-stat-label">ciclo de adesão</div></div>
    </div>
  </div>
</div>

<section class="section" style="padding-top:3rem;">
  <div class="section-inner" style="max-width:1100px;">
    <div class="party-layout">
      <div class="party-summary" data-animate>
        <span class="section-label">Resumo</span>
        <h2 class="section-title">Sobre o programa</h2>
        <p>O Programa P.A.R.T.Y. Brasil (Prevent Alcohol and Risk-Related Trauma in Youth) é uma estratégia internacional de prevenção primária do trauma, com foco na conscientização de jovens sobre comportamentos de risco, especialmente no trânsito.</p>
        <p>A implementação pelas Ligas Acadêmicas segue diretrizes do CoBraLT em parceria com o Observatório Nacional de Segurança Viária (ONSV), incluindo metodologia experiencial, simulações, vivências, discussão guiada, monitoramento de indicadores e certificação dos núcleos ativos.</p>
      </div>

      <div class="party-highlights" data-animate data-animate-delay="1">
        <span class="section-label">Pontos-chave</span>
        <ul>
          <li>Adesão voltada a Ligas Acadêmicas de Trauma filiadas ao CoBraLT.</li>
          <li>Atividades preferencialmente em formato de imersão educativa de 4 a 5 horas.</li>
          <li>Etapas previstas: acolhimento, simulação do evento traumático, jornada do paciente, depoimentos e consolidação do aprendizado.</li>
          <li>Exige equipe executora, coordenação docente e discente, parcerias institucionais e envio de relatórios periódicos.</li>
        </ul>
      </div>
    </div>

    <div class="party-docs-grid" aria-label="Documentos do Programa P.A.R.T.Y. Brasil">
      <?php foreach ($documents as $i => $doc): ?>
      <article class="party-doc-card" data-animate data-animate-delay="<?= $i + 2 ?>">
        <div>
          <span class="section-label"><?= htmlspecialchars($doc['label']) ?></span>
          <h3><?= htmlspecialchars($doc['title']) ?></h3>
          <p><?= htmlspecialchars($doc['summary']) ?></p>
          <span class="party-doc-meta"><?= htmlspecialchars($doc['pages']) ?> · PDF</span>
        </div>
        <div class="party-doc-actions">
          <a href="<?= htmlspecialchars($doc['open_url']) ?>" class="btn btn-primary" target="_blank" rel="noopener noreferrer">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M14 3h7v7"/><path d="M10 14 21 3"/><path d="M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5"/></svg>
            Abrir em tela cheia
          </a>
          <a href="<?= htmlspecialchars($doc['url']) ?>" class="btn btn-navy" download="<?= htmlspecialchars($doc['download']) ?>">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><path d="M7 10l5 5 5-5"/><path d="M12 15V3"/></svg>
            Baixar PDF
          </a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section party-pdf-section" aria-labelledby="party-viewers-title">
  <div class="section-inner" style="max-width:1180px;">
    <div class="section-header" data-animate>
      <div class="divider" aria-hidden="true"></div>
      <span class="section-label">Visualização online</span>
      <h2 class="section-title" id="party-viewers-title">Documentos do Programa P.A.R.T.Y. Brasil</h2>
      <p class="section-subtitle">Consulte os PDFs diretamente na página ou abra cada documento em uma nova guia para leitura em tela cheia.</p>
    </div>

    <div class="party-viewers">
      <?php foreach ($documents as $i => $doc): ?>
      <div class="party-viewer-shell" data-animate data-animate-delay="<?= $i ?>">
        <div class="party-viewer-header">
          <div>
            <span class="section-label"><?= htmlspecialchars($doc['label']) ?></span>
            <h3 class="party-viewer-title"><?= htmlspecialchars($doc['title']) ?></h3>
          </div>
          <a href="<?= htmlspecialchars($doc['open_url']) ?>" class="btn btn-sm btn-navy" target="_blank" rel="noopener noreferrer">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M14 3h7v7"/><path d="M10 14 21 3"/><path d="M21 14v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5"/></svg>
            Nova guia
          </a>
        </div>
        <object class="party-pdf-frame" data="<?= htmlspecialchars($doc['viewer_url']) ?>" type="application/pdf" aria-label="Visualizador de <?= htmlspecialchars($doc['title']) ?>">
          <div class="pdf-fallback">
            <span class="section-label">Visualização indisponível</span>
            <h3>Não foi possível carregar este PDF nesta área.</h3>
            <p>Abra o documento em uma nova guia ou baixe o arquivo para leitura completa.</p>
            <div class="pdf-fallback-actions">
              <a href="<?= htmlspecialchars($doc['open_url']) ?>" class="btn btn-primary" target="_blank" rel="noopener noreferrer">Abrir PDF</a>
              <a href="<?= htmlspecialchars($doc['url']) ?>" class="btn btn-navy" download="<?= htmlspecialchars($doc['download']) ?>">Baixar PDF</a>
            </div>
          </div>
        </object>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<style>
.party-layout {
  display: grid;
  grid-template-columns: minmax(0, 1.25fr) minmax(280px, 0.9fr);
  gap: 1.5rem;
  align-items: stretch;
}
.party-summary,
.party-highlights,
.party-doc-card,
.party-viewer-shell {
  background: var(--white);
  border: 1px solid var(--slate-200);
  border-radius: var(--radius-lg);
  box-shadow: var(--shadow-sm);
}
.party-summary {
  padding: 1.75rem;
}
.party-summary p {
  color: var(--slate-600);
  font-size: 0.9rem;
  line-height: 1.8;
  margin-bottom: 0.9rem;
}
.party-highlights {
  padding: 1.5rem;
}
.party-highlights ul {
  display: flex;
  flex-direction: column;
  gap: 0.85rem;
  list-style: none;
  padding: 0;
  margin: 0.35rem 0 0;
}
.party-highlights li {
  position: relative;
  padding-left: 1rem;
  color: var(--slate-600);
  font-size: 0.82rem;
  line-height: 1.65;
}
.party-highlights li::before {
  content: '';
  position: absolute;
  left: 0;
  top: 0.72em;
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: var(--sky);
}
.party-docs-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(0, 1fr));
  gap: 1.25rem;
  margin-top: 1.5rem;
}
.party-doc-card {
  padding: 1.5rem;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  gap: 1.25rem;
}
.party-doc-card h3 {
  font-family: var(--font-display);
  color: var(--navy);
  font-size: 1rem;
  line-height: 1.3;
  margin: 0 0 0.65rem;
}
.party-doc-card p {
  color: var(--slate-600);
  font-size: 0.82rem;
  line-height: 1.65;
  margin: 0 0 0.75rem;
}
.party-doc-meta {
  display: inline-flex;
  color: var(--sky-dark);
  background: #E0F2FE;
  border-radius: var(--radius-full);
  font-size: 0.68rem;
  font-weight: 700;
  padding: 3px 9px;
}
.party-doc-actions {
  display: flex;
  gap: 0.75rem;
  flex-wrap: wrap;
}
.party-pdf-section {
  padding-top: 1rem;
  background: linear-gradient(180deg, var(--off-white) 0%, #fff 100%);
}
.party-viewers {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}
.party-viewer-shell {
  overflow: hidden;
}
.party-viewer-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  padding: 1rem 1.25rem;
  border-bottom: 1px solid var(--slate-200);
  background: #fff;
}
.party-viewer-title {
  margin: 0;
  color: var(--navy);
  font-family: var(--font-display);
  font-size: 1rem;
  font-weight: 800;
  line-height: 1.25;
}
.party-pdf-frame {
  display: block;
  width: 100%;
  height: min(78vh, 860px);
  min-height: 620px;
  border: 0;
  background: var(--slate-100);
}
@media (max-width: 900px) {
  .party-layout,
  .party-docs-grid {
    grid-template-columns: 1fr;
  }
}
@media (max-width: 600px) {
  .party-summary,
  .party-highlights,
  .party-doc-card {
    padding: 1.25rem;
  }
  .party-doc-actions .btn {
    width: 100%;
    justify-content: center;
  }
  .party-viewer-header {
    align-items: flex-start;
    flex-direction: column;
  }
  .party-viewer-header .btn {
    width: 100%;
    justify-content: center;
  }
  .party-pdf-frame {
    height: 70vh;
    min-height: 480px;
  }
}
</style>

</main>

<?php layout_footer('../'); ?>
