<?php
/**
 * admin/_auth.php
 * Inclua no TOPO de toda página do painel admin:
 *   require_once __DIR__ . '/_auth.php';
 */

declare(strict_types=1);

// Credenciais centralizadas
require_once dirname(__DIR__) . '/config.php';

session_start([
    'cookie_httponly' => true,
    'cookie_secure'   => isset($_SERVER['HTTPS']),
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true,
]);

// Sessão expira após 4 horas de inatividade
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

// Proteção contra session hijacking (IP binding)
$currentIp = $_SERVER['REMOTE_ADDR'] ?? '';
if (!empty($_SESSION['admin_ip']) && $_SESSION['admin_ip'] !== $currentIp) {
    session_destroy();
    header('Location: /admin/login.html?security=1');
    exit;
}

// Renova timestamp de atividade
$_SESSION['login_time'] = time();

// ─── BANCO DE DADOS ───────────────────────────────────────
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

// ─── HELPERS ──────────────────────────────────────────────
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function json_ok(array $data = []): never {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => true] + $data);
    exit;
}

function json_fail(string $message, int $code = 400): never {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

// ─── SLUG GENERATOR ───────────────────────────────────────
function make_slug(string $str): string {
    $str = mb_strtolower(trim($str), 'UTF-8');
    $str = preg_replace('/[áàãâä]/u', 'a', $str);
    $str = preg_replace('/[éèêë]/u', 'e', $str);
    $str = preg_replace('/[íìîï]/u', 'i', $str);
    $str = preg_replace('/[óòõôö]/u', 'o', $str);
    $str = preg_replace('/[úùûü]/u', 'u', $str);
    $str = preg_replace('/[ç]/u', 'c', $str);
    $str = preg_replace('/[^a-z0-9\-]/', '-', $str);
    $str = preg_replace('/-+/', '-', $str);
    return trim($str, '-');
}

// ─── VERIFICAÇÃO DE ROLE ──────────────────────────────────
function require_role(string ...$roles): void {
    global $adminRole;
    if (!in_array($adminRole, $roles, true)) {
        http_response_code(403);
        exit('<p>Acesso negado. Seu perfil não tem permissão para esta ação.</p>');
    }
}

// Usuário logado
$adminUser = $_SESSION['admin_user'] ?? 'Admin';
$adminRole = $_SESSION['admin_role'] ?? 'editor';
$adminId   = (int)($_SESSION['admin_id'] ?? 0);
