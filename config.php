<?php
/**
 * config.php — Configurações centrais do CoBraLT.
 *
 * Para credenciais reais, crie um arquivo config.local.php na raiz
 * ou configure variáveis de ambiente na hospedagem. Esse arquivo pode
 * ser versionado sem expor senha de banco.
 */

declare(strict_types=1);

$localConfig = __DIR__ . '/config.local.php';
if (is_file($localConfig)) {
    require $localConfig;
}

function define_if_missing(string $name, $value): void {
    if (!defined($name)) {
        define($name, $value);
    }
}

// Banco de dados
define_if_missing('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define_if_missing('DB_NAME', getenv('DB_NAME') ?: '');
define_if_missing('DB_USER', getenv('DB_USER') ?: '');
define_if_missing('DB_PASS', getenv('DB_PASS') ?: '');

// Uploads
define_if_missing('UPLOAD_DIR', __DIR__ . '/assets/img/uploads/');
define_if_missing('UPLOAD_URL', '/assets/img/uploads/');
define_if_missing('UPLOAD_MAX_MB', 5);

// Site
define_if_missing('SITE_NAME', 'CoBraLT');
define_if_missing('SITE_URL', getenv('SITE_URL') ?: 'https://cobralT.org.br');
