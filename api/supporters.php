<?php
/** CRUD administrativo de apoiadores. */

declare(strict_types=1);

require_once dirname(__DIR__) . '/admin/_auth.php';
require_once dirname(__DIR__) . '/includes/supporters.php';

$db = getDB();
supporters_seed_if_empty($db);
$action = (string)($_REQUEST['action'] ?? '');

function supporter_admin_image_path(string $value): string {
    $value = ltrim(str_replace('\\', '/', trim($value)), '/');
    if (
        $value === ''
        || str_contains($value, '..')
        || !preg_match('#^assets/img/(?:apoiadores|uploads)/[a-zA-Z0-9._/-]+$#', $value)
    ) {
        json_fail('Selecione uma imagem válida da galeria ou envie um novo arquivo.');
    }
    return $value;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'list') {
    $rows = $db->query(
        'SELECT id, nome, instituicao, imagem, ativo, ordem, created_at, updated_at
         FROM apoiadores ORDER BY ordem ASC, nome ASC'
    )->fetchAll();
    json_ok(['supporters' => $rows]);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'get') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $db->prepare('SELECT * FROM apoiadores WHERE id = ?');
    $stmt->execute([$id]);
    $supporter = $stmt->fetch();
    if (!$supporter) {
        json_fail('Apoiador não encontrado.', 404);
    }
    json_ok(['supporter' => $supporter]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($action, ['create', 'update'], true)) {
    csrf_verify();

    $id = (int)($_POST['id'] ?? 0);
    $name = trim((string)($_POST['nome'] ?? ''));
    $institution = trim((string)($_POST['instituicao'] ?? ''));
    $image = supporter_admin_image_path((string)($_POST['imagem'] ?? ''));
    $active = !empty($_POST['ativo']) ? 1 : 0;
    $order = max(0, min(999999, (int)($_POST['ordem'] ?? 0)));

    if ($name === '') {
        json_fail('O nome do apoiador é obrigatório.');
    }
    if (mb_strlen($name, 'UTF-8') > 180) {
        json_fail('O nome deve ter no máximo 180 caracteres.');
    }
    if (mb_strlen($institution, 'UTF-8') > 255) {
        json_fail('A instituição deve ter no máximo 255 caracteres.');
    }

    if ($action === 'create') {
        if ($order === 0) {
            $order = (int)$db->query('SELECT COALESCE(MAX(ordem), 0) + 10 FROM apoiadores')->fetchColumn();
        }
        $stmt = $db->prepare(
            'INSERT INTO apoiadores (nome, instituicao, imagem, ativo, ordem)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$name, $institution ?: null, $image, $active, $order]);
        json_ok(['id' => (int)$db->lastInsertId()]);
    }

    if ($id <= 0) {
        json_fail('Apoiador inválido.', 422);
    }
    $stmt = $db->prepare(
        'UPDATE apoiadores
         SET nome = ?, instituicao = ?, imagem = ?, ativo = ?, ordem = ?, updated_at = CURRENT_TIMESTAMP
         WHERE id = ?'
    );
    $stmt->execute([$name, $institution ?: null, $image, $active, $order, $id]);
    if ($stmt->rowCount() === 0) {
        $check = $db->prepare('SELECT id FROM apoiadores WHERE id = ?');
        $check->execute([$id]);
        if (!$check->fetch()) {
            json_fail('Apoiador não encontrado.', 404);
        }
    }
    json_ok(['id' => $id]);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
    csrf_verify();
    require_role('superadmin', 'admin');
    $id = (int)($_POST['id'] ?? 0);
    if ($id <= 0) {
        json_fail('Apoiador inválido.', 422);
    }
    $stmt = $db->prepare('DELETE FROM apoiadores WHERE id = ?');
    $stmt->execute([$id]);
    json_ok();
}

json_fail('Ação inválida.', 405);
