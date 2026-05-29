<?php
/**
 * Copie este arquivo para config.local.php e preencha com os dados reais.
 * O config.local.php fica ignorado pelo Git.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'nome_do_banco');
define('DB_USER', 'usuario_do_banco');
define('DB_PASS', 'senha_do_banco');
define('DB_DRIVER', 'mysql'); // use 'sqlite' para desenvolvimento local sem MySQL
define('SQLITE_PATH', __DIR__ . '/storage/cobralt.sqlite');

define('SITE_URL', 'https://cobralT.org.br');
