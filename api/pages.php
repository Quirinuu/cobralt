<?php
/**
 * api/pages.php — CRUD de páginas estáticas
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/admin/_auth.php';
require_role('superadmin', 'admin');

$db     = getDB();
$action = $_REQUEST['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'list') {
    $rows = $db->query(
        'SELECT p.id, p.title, p.slug, p.status, p.created_at, u.username AS author
         FROM pages p JOIN admin_users u ON u.id = p.author_id
         ORDER BY p.created_at DESC'
    );
    json_ok(['pages' => $rows->fetchAll()]);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get') {
    $id   = (int)($_GET['id'] ?? 0);
    $stmt = $db->prepare('SELECT * FROM pages WHERE id = ?');
    $stmt->execute([$id]);
    $page = $stmt->fetch();
    if (!$page) json_fail('Página não encontrada.', 404);
    json_ok(['page' => $page]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create', 'update'], true)) {
    csrf_verify();

    $id      = (int)($_POST['id'] ?? 0);
    $title   = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $status  = in_array($_POST['status'] ?? '', ['draft', 'published']) ? $_POST['status'] : 'draft';

    if (empty($title))   json_fail('O título é obrigatório.');
    if (empty($content)) json_fail('O conteúdo não pode estar vazio.');

    $slug  = make_slug($title);
    $check = $db->prepare('SELECT id FROM pages WHERE slug = ? AND id != ?');
    $check->execute([$slug, $id]);
    if ($check->fetch()) $slug = $slug . '-' . time();

    if ($action === 'create') {
        $stmt = $db->prepare('INSERT INTO pages (author_id, title, slug, content, status) VALUES (?,?,?,?,?)');
        $stmt->execute([$adminId, $title, $slug, $content, $status]);
        json_ok(['id' => (int)$db->lastInsertId(), 'slug' => $slug]);
    } else {
        $stmt = $db->prepare('UPDATE pages SET title=?, slug=?, content=?, status=? WHERE id=?');
        $stmt->execute([$title, $slug, $content, $status, $id]);
        json_ok(['slug' => $slug]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
    csrf_verify();
    $id = (int)($_POST['id'] ?? 0);
    $db->prepare('DELETE FROM pages WHERE id = ?')->execute([$id]);
    json_ok();
}

json_fail('Ação inválida.', 405);
