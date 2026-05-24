<?php
/**
 * Public renderer for pages created in the admin builder.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/layout.php';
require_once dirname(__DIR__) . '/includes/page_builder.php';

$slug = trim((string)($_GET['slug'] ?? ''));
$slug = preg_replace('/[^a-z0-9\-]/i', '', $slug) ?? '';

if ($slug === '') {
    http_response_code(404);
    layout_head('Pagina nao encontrada', 'Pagina nao encontrada no CoBraLT.');
    layout_header('', '../', '', '../pages/');
    echo '<main id="main-content"><section class="section"><div class="section-inner"><h1 class="section-title">Pagina nao encontrada</h1></div></section></main>';
    layout_footer('../');
    exit;
}

try {
    $db = getPublicDB();
    $stmt = $db->prepare("SELECT title, slug, content, status FROM pages WHERE slug = ? AND status = 'published' LIMIT 1");
    $stmt->execute([$slug]);
    $page = $stmt->fetch();
} catch (PDOException $e) {
    $page = null;
}

if (!$page) {
    http_response_code(404);
    layout_head('Pagina nao encontrada', 'Pagina nao encontrada no CoBraLT.');
    layout_header('', '../', '', '../pages/');
    echo '<main id="main-content"><section class="section"><div class="section-inner"><h1 class="section-title">Pagina nao encontrada</h1><p class="section-subtitle">A pagina solicitada nao esta publicada ou nao existe.</p><a href="../index.php" class="btn btn-secondary">Voltar ao inicio</a></div></section></main>';
    layout_footer('../');
    exit;
}

$builder = pb_decode_content($page['content'] ?? '', $page['title']);
$desc = pb_summary($builder['blocks'], $page['title']);

layout_head($page['title'], $desc, '../');
layout_header('', '../', '', '../pages/');
?>
<main id="main-content" class="pb-page">
  <?php pb_render_blocks($builder['blocks']); ?>
</main>
<?php layout_footer('../'); ?>
