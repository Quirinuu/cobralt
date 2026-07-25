<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/layout.php';
require_once dirname(__DIR__) . '/includes/posts_helpers.php';

$slug = trim($_GET['slug'] ?? '');

if (empty($slug)) {
    header('Location: noticias');
    exit;
}

try {
    $db   = getPublicDB();
    $stmt = $db->prepare(
        "SELECT title, slug, excerpt, content, category, tipo, cover_image, published_at
         FROM posts
         WHERE slug = ? AND status = 'published' AND tipo = 'noticias'
         LIMIT 1"
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
    echo '<main id="main-content"><div style="text-align:center;padding:6rem 1rem;"><h1 style="font-family:var(--font-display);color:var(--navy);">Publicação não encontrada</h1><p style="color:var(--slate-500);margin:1rem 0 2rem;">O conteúdo que você procura não existe ou foi removido.</p><a href="noticias" class="btn btn-secondary">← Voltar</a></div></main>';
    layout_footer('../');
    exit;
}

$tipoPage = 'noticias';
$label    = 'Notícias';
$dt       = $post['published_at'] ? fmtDate($post['published_at']) : '';
$dtIso    = $post['published_at'] ? substr($post['published_at'], 0, 10) : '';
$coverSrc = post_cover_src($post, '../', $post['tipo'] ?? 'noticias');

// 3 posts relacionados do mesmo tipo
try {
    $rel = $db->prepare(
        "SELECT title, slug, excerpt, cover_image, published_at FROM posts
         WHERE status = 'published' AND tipo = 'noticias' AND slug != ?
         ORDER BY published_at DESC LIMIT 3"
    );
    $rel->execute([$slug]);
    $relacionados = $rel->fetchAll();
} catch (PDOException $e) {
    $relacionados = [];
}

layout_head(h($post['title']), h($post['excerpt'] ?? ''));
layout_header('noticias');
?>

<main id="main-content">

<!-- HERO -->
<div class="page-hero">
  <div class="page-hero-inner">
    <nav class="breadcrumb">
      <a href="../index">Início</a>
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
<section class="section post-article-section">
  <div class="section-inner post-article-shell">

    <div class="post-feature-layout">
      <figure class="post-cover-feature">
        <img src="<?= h($coverSrc) ?>" alt="<?= h($post['title']) ?>">
        <figcaption>
          <?= $slug === 'dr-wellington-paraninfo-turma-iv'
              ? 'Registro da homenagem acadêmica ao Dr. Wellington José dos Santos.'
              : 'Imagem de destaque da publicação.' ?>
        </figcaption>
      </figure>
      <aside class="post-feature-summary" aria-label="Resumo da notícia">
        <span class="section-label">Em destaque</span>
        <p class="post-feature-lead"><?= h($post['excerpt'] ?? '') ?></p>
        <div class="post-feature-meta">
          <span><?= h($post['category'] ?? $label) ?></span>
          <?php if ($dt): ?><time datetime="<?= h($dtIso) ?>"><?= h($dt) ?></time><?php endif; ?>
        </div>
        <?php if ($slug === 'dr-wellington-paraninfo-turma-iv'): ?>
          <div class="post-feature-note">
            <strong>Educação que inspira</strong>
            <p>Uma homenagem que reconhece o valor de quem orienta, ensina e ajuda novas gerações a construir seu caminho.</p>
          </div>
        <?php endif; ?>
      </aside>
    </div>

    <!-- Corpo do post -->
    <article class="post-content">
      <?= $post['content'] /* HTML salvo pelo editor — já sanitizado no admin */ ?>
    </article>

    <!-- Voltar -->
    <div style="margin-top:3rem;padding-top:2rem;border-top:1px solid var(--slate-200);">
      <a href="<?= h($tipoPage) ?>" class="btn btn-secondary">
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
          $rCover = post_cover_src($r, '../', 'noticias');
        ?>
        <a href="post?slug=<?= h($r['slug']) ?>" style="text-decoration:none;">
          <div class="news-card" style="cursor:pointer;">
            <div class="news-thumb">
              <img src="<?= h($rCover) ?>" alt="<?= h($r['title']) ?>" loading="lazy">
            </div>
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
