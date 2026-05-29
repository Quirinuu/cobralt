<?php
/**
 * api/posts.php - CRUD de posts/noticias.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/admin/_auth.php';

$db     = getDB();
$action = $_REQUEST['action'] ?? '';

$allowedStatus = ['draft', 'published'];
$allowedTipos  = ['noticias', 'eventos', 'projetos', 'educacao'];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'list') {
    $status = $_GET['status'] ?? '';
    $status = in_array($status, $allowedStatus, true) ? $status : '';
    $where  = $status ? 'WHERE p.status = ?' : '';
    $params = $status ? [$status] : [];

    $rows = $db->prepare(
        "SELECT p.id, p.title, p.slug, p.status, p.tipo, p.category, p.published_at, p.created_at,
                COALESCE(u.username, 'Usuario removido') AS author
         FROM posts p
         LEFT JOIN admin_users u ON u.id = p.author_id
         {$where}
         ORDER BY p.created_at DESC"
    );
    $rows->execute($params);
    json_ok(['posts' => $rows->fetchAll()]);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get') {
    $id   = (int)($_GET['id'] ?? 0);
    $stmt = $db->prepare('SELECT * FROM posts WHERE id = ?');
    $stmt->execute([$id]);
    $post = $stmt->fetch();
    if (!$post) {
        json_fail('Post nao encontrado.', 404);
    }
    json_ok(['post' => $post]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create', 'update'], true)) {
    csrf_verify();

    $id        = (int)($_POST['id'] ?? 0);
    $title     = trim($_POST['title'] ?? '');
    $slugInput = trim($_POST['slug'] ?? '');
    $content   = sanitize_editor_html(trim($_POST['content'] ?? ''));
    $excerpt   = trim($_POST['excerpt'] ?? '');
    $cat       = trim($_POST['category'] ?? '');
    $tipo      = $_POST['tipo'] ?? 'noticias';
    $tipo      = in_array($tipo, $allowedTipos, true) ? $tipo : 'noticias';
    $status    = $_POST['status'] ?? 'draft';
    $status    = in_array($status, $allowedStatus, true) ? $status : 'draft';
    $cover     = trim($_POST['cover_image'] ?? '');

    if ($title === '') {
        json_fail('O titulo e obrigatorio.');
    }
    if ($content === '') {
        json_fail('O conteudo nao pode ficar vazio.');
    }
    if ($action === 'update' && $id <= 0) {
        json_fail('Post invalido.', 422);
    }

    $slug = make_slug($slugInput !== '' ? $slugInput : $title);

    $check = $db->prepare('SELECT id FROM posts WHERE slug = ? AND id != ?');
    $check->execute([$slug, $id]);
    if ($check->fetch()) {
        if ($slugInput !== '') {
            json_fail('Este slug ja esta em uso. Escolha outro.', 409);
        }
        $slug = $slug . '-' . time();
    }

    $publishedAt = ($status === 'published') ? date('Y-m-d H:i:s') : null;

    if ($action === 'create') {
        $stmt = $db->prepare(
            'INSERT INTO posts (author_id, title, slug, tipo, excerpt, content, cover_image, category, status, published_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$adminId, $title, $slug, $tipo, $excerpt, $content, $cover ?: null, $cat ?: null, $status, $publishedAt]);
        json_ok(['id' => (int)$db->lastInsertId(), 'slug' => $slug]);
    }

    $owner = $db->prepare('SELECT author_id FROM posts WHERE id = ?');
    $owner->execute([$id]);
    $row = $owner->fetch();
    if (!$row) {
        json_fail('Post nao encontrado.', 404);
    }
    if ((int)$row['author_id'] !== $adminId && !in_array($adminRole, ['superadmin', 'admin'], true)) {
        json_fail('Sem permissao para editar este post.', 403);
    }

    $stmt = $db->prepare(
        'UPDATE posts
         SET title=?, slug=?, tipo=?, excerpt=?, content=?, cover_image=?, category=?, status=?, published_at=COALESCE(published_at,?)
         WHERE id=?'
    );
    $stmt->execute([$title, $slug, $tipo, $excerpt, $content, $cover ?: null, $cat ?: null, $status, $publishedAt, $id]);
    json_ok(['slug' => $slug]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
    csrf_verify();
    require_role('superadmin', 'admin');

    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        json_fail('Post invalido.', 422);
    }
    $stmt = $db->prepare('DELETE FROM posts WHERE id = ?');
    $stmt->execute([$id]);
    json_ok();
}

json_fail('Acao invalida.', 405);
