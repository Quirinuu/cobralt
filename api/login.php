<?php
/**
 * api/login.php - Autenticacao segura CoBraLT
 */

declare(strict_types=1);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';

session_start([
    'cookie_httponly' => true,
    'cookie_secure'   => isset($_SERVER['HTTPS']),
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true,
]);

$ip      = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rateKey = 'rate_' . md5($ip);

if (!isset($_SESSION[$rateKey])) {
    $_SESSION[$rateKey] = ['count' => 0, 'first' => time()];
}

$rate = &$_SESSION[$rateKey];

if (time() - $rate['first'] > 900) {
    $rate = ['count' => 0, 'first' => time()];
}

if ($rate['count'] >= 5) {
    $wait = max(1, 900 - (time() - $rate['first']));
    json_fail('Muitas tentativas. Aguarde ' . ceil($wait / 60) . ' minuto(s).', 429);
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    json_fail('Preencha usuario e senha.');
}

if (strlen($username) > 60 || strlen($password) > 128) {
    $rate['count']++;
    json_fail('Credenciais invalidas.');
}

try {
    $pdo = getPublicDB();
} catch (Throwable $e) {
    error_log('[CoBraLT] DB connection failed: ' . $e->getMessage());
    json_fail('Erro interno. Tente novamente.', 500);
}

$stmt = $pdo->prepare(
    'SELECT id, username, password_hash, role FROM admin_users WHERE username = ? AND active = 1 LIMIT 1'
);
$stmt->execute([$username]);
$user = $stmt->fetch();

$dummyHash = '$2y$12$G2x7p2oylqfHd9p2P1A62ewbGv7GWPxEWbq15xw9tNAQz9Zgr7BNq';
$hash = $user ? $user['password_hash'] : $dummyHash;

if (!$user || !password_verify($password, $hash)) {
    $rate['count']++;
    error_log("[CoBraLT] Failed login: {$username} from {$ip}");
    json_fail('Usuario ou senha incorretos.', 401);
}

$rate['count'] = 0;
session_regenerate_id(true);

$_SESSION['admin_id']   = (int)$user['id'];
$_SESSION['admin_user'] = $user['username'];
$_SESSION['admin_role'] = $user['role'];
$_SESSION['admin_ip']   = $ip;
$_SESSION['admin_ua']   = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 200);
$_SESSION['login_time'] = time();

$pdo->prepare('UPDATE admin_users SET last_login = ? WHERE id = ?')->execute([date('Y-m-d H:i:s'), $user['id']]);

echo json_encode(['success' => true], JSON_UNESCAPED_UNICODE);
exit;

function json_fail(string $message, int $status = 400): never {
    http_response_code($status);
    echo json_encode(['success' => false, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}
