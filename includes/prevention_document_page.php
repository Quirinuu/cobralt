<?php
/** Renderer compartilhado para os materiais de Prevenção de Trauma. */

declare(strict_types=1);

require_once __DIR__ . '/layout.php';

/** @param array<string,mixed> $document */
function render_prevention_document(array $document): void {
    require_once __DIR__ . '/page_builder.php';
    if (pb_render_managed_page_if_exists(basename((string)($_SERVER['SCRIPT_FILENAME'] ?? ''), '.php'), 'projetos')) {
        return;
    }
    $escape = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $pdfUrl = '../assets/pdfs/' . rawurlencode((string)$document['pdf']);
    $pdfOpenUrl = $pdfUrl . '#page=1&view=FitH';
    $pdfViewerUrl = $pdfUrl . '#page=1&toolbar=1&navpanes=0&scrollbar=1&view=FitH';

    layout_head((string)$document['title'], (string)$document['description']);
    layout_header('projetos');
    ?>
<main id="main-content">
  <div class="page-hero prevention-document-hero">
    <div class="page-hero-inner">
      <nav class="breadcrumb" aria-label="Navegação estrutural">
        <a href="../index">Início</a><span>›</span><a href="projetos">Projetos</a><span>›</span><a href="prevencao-trauma">Prevenção de Trauma</a><span>›</span><span><?= $escape((string)$document['breadcrumb']) ?></span>
      </nav>
      <div class="page-hero-label">Projeto Prevenção de Trauma · 2026</div>
      <h1 class="page-hero-title"><?= $escape((string)$document['title']) ?></h1>
      <p class="page-hero-sub"><?= $escape((string)$document['subtitle']) ?></p>
      <div class="region-stats">
        <div class="region-stat"><div class="region-stat-value"><?= (int)$document['pages'] ?></div><div class="region-stat-label">páginas</div></div>
        <div class="region-stat"><div class="region-stat-value">PDF</div><div class="region-stat-label">leitura completa</div></div>
        <div class="region-stat"><div class="region-stat-value">2026</div><div class="region-stat-label">edição atual</div></div>
      </div>
    </div>
  </div>

  <section class="section" style="padding-top:3rem;">
    <div class="section-inner" style="max-width:1080px;">
      <div class="prevention-document-layout">
        <article class="prevention-document-summary" data-animate>
          <span class="section-label">Sobre a publicação</span>
          <h2 class="section-title"><?= $escape((string)$document['summary_title']) ?></h2>
          <?php foreach ($document['summary'] as $paragraph): ?>
            <p><?= $escape((string)$paragraph) ?></p>
          <?php endforeach; ?>
          <div class="prevention-document-actions" aria-label="Ações do documento">
            <a href="<?= $escape($pdfOpenUrl) ?>" class="btn btn-primary" target="_blank" rel="noopener noreferrer">Abrir em tela cheia</a>
            <a href="<?= $escape($pdfUrl) ?>" class="btn btn-navy" download="<?= $escape((string)$document['download_name']) ?>">Baixar PDF</a>
          </div>
        </article>

        <aside class="prevention-document-highlights" data-animate data-animate-delay="1">
          <span class="section-label">Conteúdo do material</span>
          <ul>
            <?php foreach ($document['highlights'] as $highlight): ?>
              <li><?= $escape((string)$highlight) ?></li>
            <?php endforeach; ?>
          </ul>
        </aside>
      </div>
      <p class="prevention-document-note">Material educativo. As orientações não substituem avaliação profissional nem os protocolos dos serviços de emergência.</p>
    </div>
  </section>

  <section class="section prevention-pdf-section" aria-labelledby="pdf-viewer-title">
    <div class="section-inner" style="max-width:1180px;">
      <div class="prevention-pdf-shell" data-animate>
        <div class="prevention-pdf-header">
          <div>
            <span class="section-label">Visualização online</span>
            <h2 id="pdf-viewer-title"><?= $escape((string)$document['title']) ?></h2>
          </div>
          <a href="<?= $escape($pdfOpenUrl) ?>" class="btn btn-sm btn-navy" target="_blank" rel="noopener noreferrer">Nova guia</a>
        </div>
        <object class="prevention-pdf-frame" data="<?= $escape($pdfViewerUrl) ?>" type="application/pdf" aria-label="Visualizador do PDF <?= $escape((string)$document['title']) ?>">
          <div class="pdf-fallback">
            <span class="section-label">Visualização indisponível</span>
            <h3>Não foi possível carregar o PDF nesta área.</h3>
            <p>Abra o documento em uma nova guia ou baixe o arquivo para leitura completa.</p>
            <div class="pdf-fallback-actions">
              <a href="<?= $escape($pdfOpenUrl) ?>" class="btn btn-primary" target="_blank" rel="noopener noreferrer">Abrir PDF</a>
              <a href="<?= $escape($pdfUrl) ?>" class="btn btn-navy" download="<?= $escape((string)$document['download_name']) ?>">Baixar PDF</a>
            </div>
          </div>
        </object>
      </div>
    </div>
  </section>
</main>

<style>
.prevention-document-hero { background:linear-gradient(135deg,#002B4E 0%,#013684 62%,#7F1D1D 100%); }
.prevention-document-layout { display:grid;grid-template-columns:minmax(0,1.35fr) minmax(280px,.85fr);gap:1.5rem;align-items:stretch; }
.prevention-document-summary,.prevention-document-highlights,.prevention-pdf-shell { background:var(--white);border:1px solid var(--slate-200);border-radius:var(--radius-lg);box-shadow:var(--shadow-sm); }
.prevention-document-summary { padding:1.75rem; }
.prevention-document-summary p { color:var(--slate-600);font-size:.9rem;line-height:1.8;margin-bottom:.9rem; }
.prevention-document-actions { display:flex;gap:.75rem;flex-wrap:wrap;margin-top:1.25rem; }
.prevention-document-highlights { padding:1.5rem; }
.prevention-document-highlights ul { display:flex;flex-direction:column;gap:.85rem;list-style:none;padding:0;margin:.35rem 0 0; }
.prevention-document-highlights li { position:relative;padding-left:1rem;color:var(--slate-600);font-size:.82rem;line-height:1.65; }
.prevention-document-highlights li::before { content:'';position:absolute;left:0;top:.72em;width:5px;height:5px;border-radius:50%;background:#B91C1C; }
.prevention-document-note { margin:1rem 0 0;color:var(--slate-500);font-size:.75rem;line-height:1.55;text-align:center; }
.prevention-pdf-section { padding-top:1rem;background:linear-gradient(180deg,var(--off-white) 0%,#fff 100%); }
.prevention-pdf-shell { overflow:hidden; }
.prevention-pdf-header { display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1rem 1.25rem;border-bottom:1px solid var(--slate-200); }
.prevention-pdf-header h2 { margin:0;color:var(--navy);font-family:var(--font-display);font-size:1rem;font-weight:800;line-height:1.25; }
.prevention-pdf-frame { display:block;width:100%;height:min(78vh,860px);min-height:620px;border:0;background:var(--slate-100); }
@media(max-width:900px){.prevention-document-layout{grid-template-columns:1fr}}
@media(max-width:600px){.prevention-document-summary,.prevention-document-highlights{padding:1.25rem}.prevention-document-actions .btn{width:100%;justify-content:center}.prevention-pdf-header{align-items:flex-start;flex-direction:column}.prevention-pdf-header .btn{width:100%;justify-content:center}.prevention-pdf-frame{height:70vh;min-height:480px}}
</style>
<?php
    layout_footer('../');
}
