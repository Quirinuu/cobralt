<?php
/**
 * includes/posts_helpers.php
 * Funções compartilhadas do módulo público de notícias.
 */

function fmtDate(string $dt): string {
    $months = ['','janeiro','fevereiro','março','abril','maio','junho',
               'julho','agosto','setembro','outubro','novembro','dezembro'];
    [$y, $m, $d] = explode('-', substr($dt, 0, 10));
    return (int)$d . ' de ' . $months[(int)$m] . ' de ' . $y;
}

function post_default_cover(string $tipo = 'noticias', string $base = '../'): string {
    return $base . 'assets/img/posts/noticia-padrao.svg?v=' . ASSET_VERSION;
}

function post_cover_src(array $post, string $base = '../', string $tipo = 'noticias'): string {
    $cover = trim((string)($post['cover_image'] ?? $post['image'] ?? ''));
    if ($cover === '') {
        return post_default_cover($tipo, $base);
    }
    if (preg_match('/^https?:\/\//i', $cover)) {
        return $cover;
    }
    $src = preg_match('/^\//', $cover) ? $cover : $base . ltrim($cover, '/');
    return $src . (strpos($src, '?') !== false ? '&' : '?') . 'v=' . ASSET_VERSION;
}

/**
 * Renderiza o grid de cards de posts
 */
function render_posts_grid(array $posts, string $tipo = 'noticias'): void {
    if (empty($posts)):
      $emptyTitle = 'Novas notícias em breve';
      $emptyText = 'Esta página está pronta para receber as próximas notícias publicadas pela equipe no painel administrativo.';
    ?>
      <div class="posts-empty-state" style="grid-column:1/-1;">
        <img src="<?= h(post_default_cover('noticias', '../')) ?>" alt="Notícias do CoBraLT" loading="lazy">
        <div class="posts-empty-state-body">
          <strong><?= h($emptyTitle) ?></strong>
          <p><?= h($emptyText) ?></p>
        </div>
      </div>
    <?php return; endif;

    foreach ($posts as $i => $p):
        $dt    = $p['published_at'] ? fmtDate($p['published_at']) : '';
        $dtIso = $p['published_at'] ? substr($p['published_at'], 0, 10) : '';
        $cat   = h($p['category'] ?? '');
        $cover = post_cover_src($p, '../', $tipo);
    ?>
    <article class="news-card" data-animate data-animate-delay="<?= $i % 3 ?>">
      <a href="post?slug=<?= rawurlencode((string)$p['slug']) ?>" class="news-card-link" aria-label="Ler notícia: <?= h($p['title']) ?>">
        <div class="news-thumb">
          <span class="news-cat"><?= $cat ?></span>
          <img src="<?= h($cover) ?>" alt="<?= h($p['title']) ?>" loading="lazy">
        </div>
        <div class="news-body">
          <div class="news-meta">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <time datetime="<?= h($dtIso) ?>"><?= h($dt) ?></time>
          </div>
          <h3><?= h($p['title']) ?></h3>
          <p><?= h($p['excerpt'] ?? '') ?></p>
          <span class="news-link">
            Ler notícia
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </span>
        </div>
      </a>
    </article>
    <?php endforeach;
}

/**
 * Busca posts por tipo do banco
 */
function get_posts_by_tipo(PDO $db, string $tipo): array {
    $stmt = $db->prepare(
        "SELECT title, slug, excerpt, category, cover_image, published_at
         FROM posts WHERE status = 'published' AND tipo = ?
         ORDER BY published_at DESC"
    );
    $stmt->execute([$tipo]);
    return $stmt->fetchAll();
}
