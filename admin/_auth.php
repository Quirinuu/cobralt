<?php
/**
 * admin/_auth.php
 * Inclua no TOPO de toda página do painel admin:
 *   require_once __DIR__ . '/_auth.php';
 */

declare(strict_types=1);

session_start([
    'cookie_httponly' => true,
    'cookie_secure'   => isset($_SERVER['HTTPS']),
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true,
]);

// Sessão expirada após 4 horas de inatividade
$timeout = 4 * 3600;

if (
    empty($_SESSION['admin_id']) ||
    empty($_SESSION['login_time']) ||
    (time() - $_SESSION['login_time']) > $timeout
) {
    session_destroy();
    header('Location: /admin/login.html?expired=1');
    exit;
}

// Verifica se o IP não mudou (proteção contra session hijacking)
$currentIp = $_SERVER['REMOTE_ADDR'] ?? '';
if (!empty($_SESSION['admin_ip']) && $_SESSION['admin_ip'] !== $currentIp) {
    session_destroy();
    header('Location: /admin/login.html?security=1');
    exit;
}

// Renova o timestamp de atividade
$_SESSION['login_time'] = time();

// ─── BANCO DE DADOS ───────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_NAME', 'SEU_BANCO_AQUI');
define('DB_USER', 'SEU_USUARIO_AQUI');
define('DB_PASS', 'SUA_SENHA_AQUI');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    }
    return $pdo;
}

// ─── CSRF TOKEN ───────────────────────────────────────────
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_verify(): void {
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        exit(json_encode(['success' => false, 'message' => 'Token de segurança inválido.']));
    }
}

// ─── HELPER SEGURO DE OUTPUT ──────────────────────────────
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Usuário logado
$adminUser = $_SESSION['admin_user'] ?? 'Admin';
$adminRole = $_SESSION['admin_role'] ?? 'editor';
