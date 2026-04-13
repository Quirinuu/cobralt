<?php
/**
 * includes/posts_helpers.php
 * Funções compartilhadas para páginas de posts (noticias, eventos, projetos, educacao)
 */

function fmtDate(string $dt): string {
    $months = ['','janeiro','fevereiro','março','abril','maio','junho',
               'julho','agosto','setembro','outubro','novembro','dezembro'];
    [$y, $m, $d] = explode('-', substr($dt, 0, 10));
    return (int)$d . ' de ' . $months[(int)$m] . ' de ' . $y;
}

$tipoEmoji = [
    'noticias' => '📰',
    'eventos'  => '📅',
    'projetos' => '🔬',
    'educacao' => '🎓',
];

$tipoLabel = [
    'noticias' => 'Notícias',
    'eventos'  => 'Eventos',
    'projetos' => 'Projetos',
    'educacao' => 'Educação',
];

/**
 * Renderiza o grid de cards de posts
 */
function render_posts_grid(array $posts, string $tipo): void {
    global $tipoEmoji;
    if (empty($posts)): ?>
      <p style="color:var(--slate-400);grid-column:1/-1;text-align:center;padding:3rem 0;">Nenhuma publicação encontrada.</p>
    <?php return; endif;

    foreach ($posts as $i => $p):
        $dt    = $p['published_at'] ? fmtDate($p['published_at']) : '';
        $dtIso = $p['published_at'] ? substr($p['published_at'], 0, 10) : '';
        $emoji = $tipoEmoji[$tipo] ?? '📄';
        $cat   = h($p['category'] ?? '');
    ?>
    <article class="news-card" data-animate data-animate-delay="<?= $i % 3 ?>">
      <div class="news-thumb">
        <span class="news-cat"><?= $cat ?></span>
        <?= $emoji ?>
      </div>
      <div class="news-body">
        <div class="news-meta">
          <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          <time datetime="<?= h($dtIso) ?>"><?= h($dt) ?></time>
        </div>
        <h3><?= h($p['title']) ?></h3>
        <p><?= h($p['excerpt'] ?? '') ?></p>
        <a href="post.php?slug=<?= h($p['slug']) ?>" class="news-link">
          Ler mais
          <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </a>
      </div>
    </article>
    <?php endforeach;
}

/**
 * Busca posts por tipo do banco
 */
function get_posts_by_tipo(PDO $db, string $tipo): array {
    $stmt = $db->prepare(
        "SELECT title, slug, excerpt, category, published_at
         FROM posts WHERE status = 'published' AND tipo = ?
         ORDER BY published_at DESC"
    );
    $stmt->execute([$tipo]);
    return $stmt->fetchAll();
}
