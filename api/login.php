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

$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    json_fail('Preencha usuario e senha.');
}

if (strlen($username) > 60 || strlen($password) > 128) {
    json_fail('Credenciais invalidas.');
}

try {
    $pdo = getPublicDB();
} catch (Throwable $e) {
    error_log('[CoBraLT] DB connection failed: ' . $e->getMessage());
    json_fail('Erro interno. Tente novamente.', 500);
}

// Rate limit no banco (a sessao pode ser descartada pelo atacante). 5 falhas/15 min por IP, 10 por usuario.
$pdo->exec('CREATE TABLE IF NOT EXISTS login_attempts (k VARCHAR(190) NOT NULL, attempted_at INT NOT NULL)');
$pdo->prepare('DELETE FROM login_attempts WHERE attempted_at < ?')->execute([time() - 900]);
$keys = ['ip:' . $ip, 'user:' . strtolower($username)];
$limits = [5, 10];
foreach ($keys as $i => $key) {
    $cnt = $pdo->prepare('SELECT COUNT(*) FROM login_attempts WHERE k = ?');
    $cnt->execute([$key]);
    if ((int)$cnt->fetchColumn() >= $limits[$i]) {
        json_fail('Muitas tentativas. Aguarde alguns minutos.', 429);
    }
}

$stmt = $pdo->prepare(
    'SELECT id, username, password_hash, role FROM admin_users WHERE username = ? AND active = 1 LIMIT 1'
);
$stmt->execute([$username]);
$user = $stmt->fetch();

$dummyHash = '$2y$12$G2x7p2oylqfHd9p2P1A62ewbGv7GWPxEWbq15xw9tNAQz9Zgr7BNq';
$hash = $user ? $user['password_hash'] : $dummyHash;

if (!$user || !password_verify($password, $hash)) {
    $ins = $pdo->prepare('INSERT INTO login_attempts (k, attempted_at) VALUES (?, ?)');
    foreach ($keys as $key) { $ins->execute([$key, time()]); }
    error_log('[CoBraLT] Failed login: ' . preg_replace('/[^\w.@-]/', '?', $username) . " from {$ip}");
    json_fail('Usuario ou senha incorretos.', 401);
}

$pdo->prepare('DELETE FROM login_attempts WHERE k = ?')->execute(['ip:' . $ip]);
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
