<?php
declare(strict_types=1);

function public_form_response(array $payload, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function handle_public_form(string $type): never {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        public_form_response(['success' => false, 'message' => 'Metodo invalido.'], 405);
    }

    if (trim((string)($_POST['website'] ?? $_POST['honeypot'] ?? '')) !== '') {
        public_form_response(['success' => true]);
    }

    $fields = [];
    foreach ($_POST as $key => $value) {
        if (is_array($value)) {
            continue;
        }
        $key = preg_replace('/[^a-z0-9_\-]/i', '', (string)$key);
        if ($key === '' || in_array($key, ['csrf_token', 'website', 'honeypot'], true)) {
            continue;
        }
        $fields[$key] = mb_substr(trim((string)$value), 0, 2000, 'UTF-8');
    }

    if (!$fields) {
        public_form_response(['success' => false, 'message' => 'Preencha os dados do formulario.'], 400);
    }

    $entry = [
        'type' => $type,
        'created_at' => date('c'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
        'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
        'fields' => $fields,
    ];

    if (store_public_form_in_database($entry)) {
        public_form_response(['success' => true]);
    }

    $storageDir = dirname(__DIR__) . '/storage';
    if (!is_dir($storageDir) && !mkdir($storageDir, 0755, true)) {
        public_form_response(['success' => false, 'message' => 'Nao foi possivel salvar a solicitacao.'], 500);
    }

    $file = $storageDir . '/form-submissions.log';
    $line = json_encode($entry, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
    if (file_put_contents($file, $line, FILE_APPEND | LOCK_EX) === false) {
        public_form_response(['success' => false, 'message' => 'Nao foi possivel salvar a solicitacao.'], 500);
    }

    public_form_response(['success' => true]);
}

function store_public_form_in_database(array $entry): bool {
    try {
        require_once dirname(__DIR__) . '/includes/db.php';
        $db = getPublicDB();
        $stmt = $db->prepare(
            'INSERT INTO form_submissions (type, payload_json, ip, user_agent, created_at) VALUES (?, ?, ?, ?, ?)'
        );
        return $stmt->execute([
            $entry['type'],
            json_encode($entry['fields'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            $entry['ip'],
            $entry['user_agent'],
            date('Y-m-d H:i:s'),
        ]);
    } catch (Throwable $e) {
        error_log('[CoBraLT] public form DB storage failed: ' . $e->getMessage());
        return false;
    }
}
