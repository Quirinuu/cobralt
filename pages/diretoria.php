<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/layout.php';

try {
    $db = getPublicDB();
    $rows = $db->query(
        "SELECT nome, cargo, especialidade, foto, grupo, bio
         FROM diretoria WHERE ativo = 1 ORDER BY grupo, ordem ASC"
    )->fetchAll();

    $diretoria = [];
    foreach ($rows as $row) {
        $diretoria[$row['grupo']][] = $row;
    }
} catch (PDOException $e) {
    $diretoria = [];
}

$ordemGrupos = [
    'Diretoria Deliberativa',
    'Diretoria de Marketing',
    'Diretoria Executiva',
    'Diretoria de Prevenção e Extensão',
    'Diretoria Interdisciplinar',
];

layout_head('Diretoria', 'Conheça os líderes do Comitê Brasileiro das Ligas do Trauma.');
layout_header('diretoria');
?>

<main id="main-content">

<div class="page-hero">
  <div class="page-hero-inner">
    <nav class="breadcrumb" aria-label="Breadcrumb">
      <a href="../index.php">Início</a><span>›</span><span>Diretoria</span>
    </nav>
    <div class="page-hero-label">Gestão 2025–2026</div>
    <h1 class="page-hero-title">Diretoria CoBraLT</h1>
    <p class="page-hero-sub">Conheça os profissionais que lideram o Comitê Brasileiro das Ligas do Trauma e trabalham para conectar instituições em todo o Brasil.</p>
  </div>
</div>

<section class="section directors-section">
  <div class="section-inner">

    <?php if (empty($diretoria)): ?>
      <p style="color:var(--slate-400);text-align:center;">Nenhum membro cadastrado.</p>
    <?php else: ?>

    <?php /* Orientador */ ?>
    <?php if (!empty($diretoria['Orientador'])): $o = $diretoria['Orientador'][0]; ?>
    <div class="advisor-card" data-animate>
      <div class="advisor-photo-wrap">
        <?php if ($o['foto']): ?>
        <img src="../<?= h($o['foto']) ?>" alt="<?= h($o['nome']) ?>" class="director-photo" onerror="this.style.display='none'">
        <?php endif; ?>
      </div>
      <div class="advisor-info">
        <span class="director-role"><?= h($o['cargo']) ?></span>
        <h3 class="director-name"><?= h($o['nome']) ?></h3>
        <span class="director-specialty"><?= h($o['especialidade'] ?? '') ?></span>
        <?php if ($o['bio']): $bioItems = json_decode($o['bio'], true) ?? []; ?>
        <ul class="advisor-bio">
          <?php foreach ($bioItems as $item): ?>
          <li><?= h($item) ?></li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>
    </div>
    <?php endif; ?>

    <?php foreach ($ordemGrupos as $grupo):
      if (empty($diretoria[$grupo])) continue; ?>
    <div class="directors-group" data-animate>
      <h3 class="directors-group-title"><?= h($grupo) ?></h3>
      <div class="directors-grid">
        <?php foreach ($diretoria[$grupo] as $m):
          $semFoto = !$m['foto'] || $m['nome'] === 'Em definição'; ?>
        <div class="director-card<?= $semFoto ? ' director-card--tbd' : '' ?>">
          <div class="director-photo-wrap<?= $semFoto ? ' director-photo-wrap--empty' : '' ?>">
            <?php if (!$semFoto): ?>
            <img src="../<?= h($m['foto']) ?>" alt="<?= h($m['nome']) ?>" class="director-photo" onerror="this.style.display='none'">
            <?php endif; ?>
          </div>
          <div class="director-role"><?= h($m['cargo']) ?></div>
          <div class="director-name"><?= h($m['nome']) ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>

    <?php endif; ?>
  </div>
</section>

</main>

<script>
document.querySelectorAll('.director-card').forEach(card => {
  card.addEventListener('click', () => {
    const sel = card.classList.contains('selected');
    document.querySelectorAll('.director-card').forEach(c => c.classList.remove('selected'));
    if (!sel) card.classList.add('selected');
  });
});
</script>

<?php layout_footer('../'); ?>
