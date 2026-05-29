<?php
/**
 * api/pages.php - CRUD de paginas estaticas do construtor.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/admin/_auth.php';
require_once dirname(__DIR__) . '/includes/page_builder.php';
require_role('superadmin', 'admin');

$db     = getDB();
$action = $_REQUEST['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'list') {
    $rows = $db->query(
        "SELECT p.id, p.title, p.slug, p.status, p.created_at,
                COALESCE(u.username, 'Usuario removido') AS author
         FROM pages p
         LEFT JOIN admin_users u ON u.id = p.author_id
         ORDER BY p.created_at DESC"
    );
    json_ok(['pages' => $rows->fetchAll()]);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get') {
    $id   = (int)($_GET['id'] ?? 0);
    $stmt = $db->prepare('SELECT * FROM pages WHERE id = ?');
    $stmt->execute([$id]);
    $page = $stmt->fetch();
    if (!$page) {
        json_fail('Pagina nao encontrada.', 404);
    }
    json_ok(['page' => $page]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create', 'update'], true)) {
    csrf_verify();

    $id         = (int)($_POST['id'] ?? 0);
    $title      = trim($_POST['title'] ?? '');
    $slugInput  = trim($_POST['slug'] ?? '');
    $blocksJson = trim($_POST['blocks_json'] ?? '');
    $content    = $blocksJson !== ''
        ? pb_encode_content($blocksJson, $title)
        : sanitize_editor_html(trim($_POST['content'] ?? ''));
    $status = in_array($_POST['status'] ?? '', ['draft', 'published'], true) ? $_POST['status'] : 'draft';

    if ($title === '') {
        json_fail('O titulo e obrigatorio.');
    }
    if ($content === '') {
        json_fail('O conteudo nao pode estar vazio.');
    }
    if ($action === 'update' && $id <= 0) {
        json_fail('Pagina invalida.', 422);
    }

    $slug = make_slug($slugInput !== '' ? $slugInput : $title);
    $check = $db->prepare('SELECT id FROM pages WHERE slug = ? AND id != ?');
    $check->execute([$slug, $id]);
    if ($check->fetch()) {
        if ($slugInput !== '') {
            json_fail('Este slug ja esta em uso. Escolha outro.', 409);
        }
        $slug = $slug . '-' . time();
    }

    if ($action === 'create') {
        $stmt = $db->prepare('INSERT INTO pages (author_id, title, slug, content, status) VALUES (?,?,?,?,?)');
        $stmt->execute([$adminId, $title, $slug, $content, $status]);
        json_ok(['id' => (int)$db->lastInsertId(), 'slug' => $slug]);
    }

    $exists = $db->prepare('SELECT id FROM pages WHERE id = ?');
    $exists->execute([$id]);
    if (!$exists->fetch()) {
        json_fail('Pagina nao encontrada.', 404);
    }

    $stmt = $db->prepare('UPDATE pages SET title=?, slug=?, content=?, status=? WHERE id=?');
    $stmt->execute([$title, $slug, $content, $status, $id]);
    json_ok(['slug' => $slug]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
    csrf_verify();
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        json_fail('Pagina invalida.', 422);
    }
    $db->prepare('DELETE FROM pages WHERE id = ?')->execute([$id]);
    json_ok();
}

json_fail('Acao invalida.', 405);
