<?php
/**
 * api/login.php — Autenticação segura CoBraLT
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
error_log('[DEBUG] username: ' . ($_POST['username'] ?? 'VAZIO'));
error_log('[DEBUG] password: ' . ($_POST['password'] ?? 'VAZIO'));

// ─── SESSÃO ───────────────────────────────────────────────
session_start([
    'cookie_httponly' => true,
    'cookie_secure'   => isset($_SERVER['HTTPS']),
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true,
]);

// ─── RATE LIMITING (5 tentativas / 15 min por IP) ─────────
$ip      = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rateKey = 'rate_' . md5($ip);

if (!isset($_SESSION[$rateKey])) {
    $_SESSION[$rateKey] = ['count' => 0, 'first' => time()];
}

$rate = &$_SESSION[$rateKey];

if (time() - $rate['first'] > 900) {
    $rate = ['count' => 0, 'first' => time()];
}

//if ($rate['count'] >= 5) {
  //  $wait = 900 - (time() - $rate['first']);
  //  json_fail("Muitas tentativas. Aguarde " . ceil($wait / 60) . " minuto(s).");
//}

// ─── INPUTS ───────────────────────────────────────────────
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    json_fail('Preencha usuário e senha.');
}

if (strlen($username) > 60 || strlen($password) > 128) {
    $rate['count']++;
    json_fail('Credenciais inválidas.');
}

// ─── BANCO ────────────────────────────────────────────────
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER, DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    error_log('[CoBraLT] DB connection failed: ' . $e->getMessage());
    json_fail('Erro interno. Tente novamente.');
}

// ─── BUSCA USUÁRIO ────────────────────────────────────────
$stmt = $pdo->prepare(
    'SELECT id, username, password_hash, role FROM admin_users WHERE username = ? AND active = 1 LIMIT 1'
);
$stmt->execute([$username]);
$user = $stmt->fetch();

// Timing-safe: verifica mesmo se usuário não existe
$dummyHash = '$2y$12$invaliddummyhashtopreventtimingattacks000000000000000';
$hash = $user ? $user['password_hash'] : $dummyHash;

if (!$user || !password_verify($password, $hash)) {
    $rate['count']++;
    error_log("[CoBraLT] Failed login: {$username} from {$ip}");
    json_fail('Usuário ou senha incorretos.');
}

// ─── SUCESSO ──────────────────────────────────────────────
$rate['count'] = 0;
session_regenerate_id(true);

$_SESSION['admin_id']   = $user['id'];
$_SESSION['admin_user'] = $user['username'];
$_SESSION['admin_role'] = $user['role'];
$_SESSION['admin_ip']   = $ip;
$_SESSION['admin_ua']   = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 200);
$_SESSION['login_time'] = time();

$pdo->prepare('UPDATE admin_users SET last_login = NOW() WHERE id = ?')->execute([$user['id']]);

echo json_encode(['success' => true]);
exit;

function json_fail(string $message): never {
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}
