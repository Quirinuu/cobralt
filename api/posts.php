<?php
/**
 * api/posts.php — CRUD de posts/notícias
 * Requer sessão admin válida.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/admin/_auth.php';

$db     = getDB();
$action = $_REQUEST['action'] ?? '';

// ─── LISTAR ───────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'list') {
    $status = $_GET['status'] ?? '';
    $where  = $status ? 'WHERE p.status = ?' : '';
    $params = $status ? [$status] : [];

    $rows = $db->prepare(
        "SELECT p.id, p.title, p.slug, p.status, p.category, p.published_at, p.created_at,
                u.username AS author
         FROM posts p
         JOIN admin_users u ON u.id = p.author_id
         {$where}
         ORDER BY p.created_at DESC"
    );
    $rows->execute($params);
    json_ok(['posts' => $rows->fetchAll()]);
}

// ─── BUSCAR UM ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get') {
    $id   = (int)($_GET['id'] ?? 0);
    $stmt = $db->prepare('SELECT * FROM posts WHERE id = ?');
    $stmt->execute([$id]);
    $post = $stmt->fetch();
    if (!$post) json_fail('Post não encontrado.', 404);
    json_ok(['post' => $post]);
}

// ─── CRIAR / EDITAR ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create', 'update'], true)) {
    csrf_verify();

    $id      = (int)($_POST['id'] ?? 0);
    $title   = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $cat     = trim($_POST['category'] ?? '');
    $status  = in_array($_POST['status'] ?? '', ['draft', 'published']) ? $_POST['status'] : 'draft';
    $cover   = trim($_POST['cover_image'] ?? '');

    if (empty($title)) json_fail('O título é obrigatório.');
    if (empty($content)) json_fail('O conteúdo não pode ficar vazio.');

    $slug = make_slug($title);

    // Garante slug único
    $check = $db->prepare('SELECT id FROM posts WHERE slug = ? AND id != ?');
    $check->execute([$slug, $id]);
    if ($check->fetch()) {
        $slug = $slug . '-' . time();
    }

    $publishedAt = ($status === 'published') ? date('Y-m-d H:i:s') : null;

    if ($action === 'create') {
        $stmt = $db->prepare(
            'INSERT INTO posts (author_id, title, slug, excerpt, content, cover_image, category, status, published_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$adminId, $title, $slug, $excerpt, $content, $cover ?: null, $cat ?: null, $status, $publishedAt]);
        json_ok(['id' => (int)$db->lastInsertId(), 'slug' => $slug]);
    } else {
        // Só superadmin/admin podem editar posts de outros
        $owner = $db->prepare('SELECT author_id FROM posts WHERE id = ?');
        $owner->execute([$id]);
        $row = $owner->fetch();
        if (!$row) json_fail('Post não encontrado.', 404);
        if ((int)$row['author_id'] !== $adminId && !in_array($adminRole, ['superadmin', 'admin'], true)) {
            json_fail('Sem permissão para editar este post.', 403);
        }

        $stmt = $db->prepare(
            'UPDATE posts SET title=?, slug=?, excerpt=?, content=?, cover_image=?, category=?, status=?, published_at=COALESCE(published_at,?)
             WHERE id=?'
        );
        $stmt->execute([$title, $slug, $excerpt, $content, $cover ?: null, $cat ?: null, $status, $publishedAt, $id]);
        json_ok(['slug' => $slug]);
    }
}

// ─── EXCLUIR ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
    csrf_verify();
    require_role('superadmin', 'admin');

    $id   = (int)($_POST['id'] ?? 0);
    $stmt = $db->prepare('DELETE FROM posts WHERE id = ?');
    $stmt->execute([$id]);
    json_ok();
}

json_fail('Ação inválida.', 405);
