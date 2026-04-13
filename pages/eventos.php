<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/layout.php';

// Lê da tabela `eventos` (agenda), não de posts
try {
    $db     = getPublicDB();
    $eventos = $db->query(
        "SELECT dia, mes, titulo, local, descricao, link, link_texto, link_externo
         FROM eventos WHERE ativo = 1 ORDER BY ordem ASC"
    )->fetchAll();
} catch (PDOException $e) {
    $eventos = [];
}

layout_head('Eventos', 'Agenda completa de eventos e congressos do CoBraLT.');
layout_header('eventos');
?>

<main id="main-content">

<div class="page-hero">
  <div class="page-hero-inner">
    <nav class="breadcrumb">
      <a href="../index.php">Início</a><span>›</span><span>Eventos</span>
    </nav>
    <div class="page-hero-label">Agenda CoBraLT</div>
    <h1 class="page-hero-title">Eventos e Congressos</h1>
    <p class="page-hero-sub">Mantenha-se atualizado com a agenda de eventos do CoBraLT e da área de trauma.</p>
    <div class="region-stats">
      <div class="region-stat"><div class="region-stat-value"><?= count($eventos) ?></div><div class="region-stat-label">eventos</div></div>
      <div class="region-stat"><div class="region-stat-value">2026</div><div class="region-stat-label">ano em curso</div></div>
      <div class="region-stat"><div class="region-stat-value">180+</div><div class="region-stat-label">ligas na rede</div></div>
    </div>
  </div>
</div>

<section class="section" style="padding-top:3rem;">
  <div class="section-inner">
    <?php if (empty($eventos)): ?>
      <p style="color:var(--slate-400);text-align:center;padding:3rem 0;">Nenhum evento cadastrado.</p>
    <?php else: ?>
    <div class="events-grid">
      <?php foreach ($eventos as $i => $ev):
        $extAttr = $ev['link_externo'] ? 'target="_blank" rel="noopener noreferrer"' : '';
        $diaStyle = mb_strlen($ev['dia']) > 2 ? 'font-size:1rem;line-height:1.1;' : '';
      ?>
      <div class="event-card" data-animate data-animate-delay="<?= $i ?>">
        <div class="event-header">
          <div class="event-date-box">
            <div class="event-day" style="<?= $diaStyle ?>"><?= h($ev['dia']) ?></div>
            <div class="event-month"><?= h($ev['mes']) ?></div>
          </div>
          <h3 class="event-title"><?= h($ev['titulo']) ?></h3>
        </div>
        <div class="event-body">
          <div class="event-location">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <?= h($ev['local']) ?>
          </div>
          <p class="event-desc"><?= h($ev['descricao']) ?></p>
          <?php if ($ev['link']): ?>
          <a href="<?= h($ev['link']) ?>" class="news-link" <?= $extAttr ?>><?= h($ev['link_texto']) ?>
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </a>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

</main>

<?php layout_footer('../'); ?>
