<?php
/**
 * admin/_auth.php
 * Include no topo de toda pagina do painel admin:
 *   require_once __DIR__ . '/_auth.php';
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';

session_start([
    'cookie_httponly' => true,
    'cookie_secure'   => isset($_SERVER['HTTPS']),
    'cookie_samesite' => 'Strict',
    'use_strict_mode' => true,
]);

function admin_auth_is_api_request(): bool {
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    return str_contains($script, '/api/');
}

function admin_auth_fail(string $reason, int $code = 401): never {
    session_destroy();
    if (admin_auth_is_api_request()) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => $reason], JSON_UNESCAPED_UNICODE);
        exit;
    }
    header('Location: /admin/login.php?expired=1');
    exit;
}

// Sessao expira apos 4 horas de inatividade.
$timeout = 4 * 3600;

if (
    empty($_SESSION['admin_id']) ||
    empty($_SESSION['login_time']) ||
    (time() - $_SESSION['login_time']) > $timeout
) {
    admin_auth_fail('Sessao expirada. Faca login novamente.', 401);
}

// Protecao leve contra sequestro de sessao sem prender a sessao ao IP.
$currentUa = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 200);
if (!empty($_SESSION['admin_ua']) && !hash_equals((string)$_SESSION['admin_ua'], $currentUa)) {
    admin_auth_fail('Sessao encerrada por seguranca. Faca login novamente.', 401);
}

$_SESSION['login_time'] = time();

function getDB(): PDO {
    return getPublicDB();
}

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
        header('Content-Type: application/json; charset=utf-8');
        exit(json_encode(['success' => false, 'message' => 'Token de seguranca invalido.'], JSON_UNESCAPED_UNICODE));
    }
}

function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function json_ok(array $data = []): never {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => true] + $data, JSON_UNESCAPED_UNICODE);
    exit;
}

function json_fail(string $message, int $code = 400): never {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => $message], JSON_UNESCAPED_UNICODE);
    exit;
}

function make_slug(string $str): string {
    $str = trim($str);
    $ascii = function_exists('iconv') ? @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str) : false;
    if (is_string($ascii) && $ascii !== '') {
        $str = $ascii;
    }
    $str = mb_strtolower($str, 'UTF-8');
    $str = preg_replace('/[^a-z0-9]+/i', '-', $str) ?? '';
    $str = preg_replace('/-+/', '-', $str) ?? '';
    $str = trim($str, '-');
    return $str !== '' ? $str : 'item';
}

function sanitize_editor_html(string $html): string {
    $allowed = '<p><br><strong><b><em><i><u><s><strike><blockquote><ol><ul><li><a><h2><h3><h4>';
    $html = strip_tags($html, $allowed);
    $html = preg_replace('/\s+on[a-z]+\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/i', '', $html) ?? $html;
    $html = preg_replace('/\s+style\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/i', '', $html) ?? $html;
    $html = preg_replace_callback('/\s+href\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', static function (array $match): string {
        $raw = trim($match[1], " \t\n\r\0\x0B\"'");
        if (preg_match('/^(https?:\/\/|mailto:|tel:|\/|#|\.\.?\/)/i', $raw)) {
            return ' href="' . htmlspecialchars($raw, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '"';
        }
        return ' href="#"';
    }, $html) ?? $html;
    return trim($html);
}

function require_role(string ...$roles): void {
    global $adminRole;
    if (!in_array($adminRole, $roles, true)) {
        http_response_code(403);
        if (admin_auth_is_api_request()) {
            header('Content-Type: application/json; charset=utf-8');
            exit(json_encode(['success' => false, 'message' => 'Sem permissao para esta acao.'], JSON_UNESCAPED_UNICODE));
        }
        exit('<p>Acesso negado. Seu perfil nao tem permissao para esta acao.</p>');
    }
}

$adminUser = $_SESSION['admin_user'] ?? 'Admin';
$adminRole = $_SESSION['admin_role'] ?? 'editor';
$adminId   = (int)($_SESSION['admin_id'] ?? 0);
