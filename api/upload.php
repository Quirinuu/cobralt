<?php
/**
 * api/upload.php — Upload seguro de imagem de capa
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/admin/_auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') json_fail('Método inválido.', 405);
csrf_verify();

$file = $_FILES['image'] ?? null;
if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
    json_fail('Nenhum arquivo recebido ou erro no upload.');
}

// Valida tamanho
$maxBytes = UPLOAD_MAX_MB * 1024 * 1024;
if ($file['size'] > $maxBytes) {
    json_fail("O arquivo excede o limite de " . UPLOAD_MAX_MB . "MB.");
}

// Valida tipo real pelo mime (não pelo nome)
$finfo    = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file['tmp_name']);
$allowed  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];

if (!array_key_exists($mimeType, $allowed)) {
    json_fail('Formato não suportado. Use JPG, PNG, WebP ou GIF.');
}

// Cria pasta se necessário
if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

// Nome único baseado em hash
$ext      = $allowed[$mimeType];
$filename = bin2hex(random_bytes(12)) . '.' . $ext;
$dest     = UPLOAD_DIR . $filename;

if (!move_uploaded_file($file['tmp_name'], $dest)) {
    json_fail('Erro ao salvar o arquivo. Verifique as permissões da pasta.');
}

json_ok(['url' => UPLOAD_URL . $filename]);
