<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/layout.php';
require_once dirname(__DIR__) . '/includes/posts_helpers.php';

$slug = trim($_GET['slug'] ?? '');

if (empty($slug)) {
    header('Location: noticias.php');
    exit;
}

try {
    $db   = getPublicDB();
    $stmt = $db->prepare(
        "SELECT title, slug, excerpt, content, category, tipo, cover_image, published_at
         FROM posts WHERE slug = ? AND status = 'published' LIMIT 1"
    );
    $stmt->execute([$slug]);
    $post = $stmt->fetch();
} catch (PDOException $e) {
    $post = null;
}

if (!$post) {
    header('HTTP/1.0 404 Not Found');
    layout_head('Publicação não encontrada');
    layout_header();
    echo '<main id="main-content"><div style="text-align:center;padding:6rem 1rem;"><h1 style="font-family:var(--font-display);color:var(--navy);">Publicação não encontrada</h1><p style="color:var(--slate-500);margin:1rem 0 2rem;">O conteúdo que você procura não existe ou foi removido.</p><a href="noticias.php" class="btn btn-primary">← Voltar</a></div></main>';
    layout_footer('../');
    exit;
}

$tipoLabel = [
    'noticias' => 'Notícias',
    'eventos'  => 'Eventos',
    'projetos' => 'Projetos',
    'educacao' => 'Educação',
];
$tipoPage = ($post['tipo'] ?? 'noticias') . '.php';
$label    = $tipoLabel[$post['tipo']] ?? 'Notícias';
$dt       = $post['published_at'] ? fmtDate($post['published_at']) : '';
$dtIso    = $post['published_at'] ? substr($post['published_at'], 0, 10) : '';

// 3 posts relacionados do mesmo tipo
try {
    $rel = $db->prepare(
        "SELECT title, slug, excerpt, published_at FROM posts
         WHERE status = 'published' AND tipo = ? AND slug != ?
         ORDER BY published_at DESC LIMIT 3"
    );
    $rel->execute([$post['tipo'], $slug]);
    $relacionados = $rel->fetchAll();
} catch (PDOException $e) {
    $relacionados = [];
}

layout_head(h($post['title']), h($post['excerpt'] ?? ''));
layout_header($post['tipo'] ?? 'noticias');
?>

<main id="main-content">

<!-- HERO -->
<div class="page-hero">
  <div class="page-hero-inner">
    <nav class="breadcrumb">
      <a href="../index.php">Início</a>
      <span>›</span>
      <a href="<?= h($tipoPage) ?>"><?= h($label) ?></a>
      <span>›</span>
      <span><?= h($post['title']) ?></span>
    </nav>
    <div class="page-hero-label"><?= h($post['category'] ?? $label) ?></div>
    <h1 class="page-hero-title" style="max-width:760px;"><?= h($post['title']) ?></h1>
    <?php if ($dt): ?>
    <p style="color:rgba(255,255,255,0.6);font-size:0.88rem;margin-top:0.75rem;">
      <time datetime="<?= h($dtIso) ?>"><?= h($dt) ?></time>
    </p>
    <?php endif; ?>
  </div>
</div>

<!-- CONTEÚDO -->
<section class="section" style="padding-top:3rem;">
  <div class="section-inner" style="max-width:780px;">

    <?php if ($post['cover_image']): ?>
    <img src="../<?= h($post['cover_image']) ?>"
         alt="<?= h($post['title']) ?>"
         style="width:100%;border-radius:var(--radius-lg);margin-bottom:2rem;object-fit:cover;max-height:420px;">
    <?php endif; ?>

    <!-- Corpo do post -->
    <div class="post-content" style="
      font-size:1.02rem;
      line-height:1.85;
      color:var(--slate-700, #374151);
    ">
      <?= $post['content'] /* HTML salvo pelo editor — já sanitizado no admin */ ?>
    </div>

    <!-- Voltar -->
    <div style="margin-top:3rem;padding-top:2rem;border-top:1px solid var(--slate-200);">
      <a href="<?= h($tipoPage) ?>" class="btn btn-navy" style="display:inline-flex;align-items:center;gap:6px;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
        Voltar para <?= h($label) ?>
      </a>
    </div>

    <!-- Relacionados -->
    <?php if (!empty($relacionados)): ?>
    <div style="margin-top:3.5rem;">
      <h2 style="font-family:var(--font-display);color:var(--navy);font-size:1.2rem;margin-bottom:1.5rem;">Mais em <?= h($label) ?></h2>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:1.25rem;">
        <?php foreach ($relacionados as $r):
          $rDt = $r['published_at'] ? fmtDate($r['published_at']) : '';
        ?>
        <a href="post.php?slug=<?= h($r['slug']) ?>" style="text-decoration:none;">
          <div class="news-card" style="cursor:pointer;">
            <div class="news-body" style="padding:1.25rem;">
              <div class="news-meta" style="margin-bottom:0.5rem;">
                <time datetime="<?= h(substr($r['published_at'],0,10)) ?>" style="font-size:0.75rem;color:var(--slate-400);"><?= h($rDt) ?></time>
              </div>
              <h3 style="font-size:0.95rem;margin:0 0 0.5rem;color:var(--navy);"><?= h($r['title']) ?></h3>
              <p style="font-size:0.83rem;color:var(--slate-500);margin:0;"><?= h(mb_substr($r['excerpt'] ?? '', 0, 90)) ?>…</p>
            </div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

  </div>
</section>

</main>

<?php layout_footer('../'); ?>
