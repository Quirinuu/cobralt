<?php
/**
 * includes/certificates.php
 * Emissão, consulta e verificação dos certificados do CoBraLT.
 */

declare(strict_types=1);

const CERT_PREFIX = 'CBL';
const CERT_ALPHABET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // 32 símbolos, sem 0/O/1/I

function cert_types(): array {
    return [
        'participante' => 'Participante',
        'palestrante'  => 'Palestrante',
        'organizador'  => 'Organizador',
        'monitor'      => 'Monitor',
    ];
}

function cert_create_tables(PDO $db): void {
    $sqlite = db_driver($db) === 'sqlite';
    $id   = $sqlite ? 'INTEGER PRIMARY KEY AUTOINCREMENT' : 'INT UNSIGNED AUTO_INCREMENT PRIMARY KEY';
    $tail = $sqlite ? '' : ' ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci';
    $db->exec("CREATE TABLE IF NOT EXISTS certificates (
        id {$id},
        code VARCHAR(20) NOT NULL UNIQUE,
        holder_name VARCHAR(180) NOT NULL,
        holder_email VARCHAR(190) NULL,
        event_name VARCHAR(220) NOT NULL,
        cert_type VARCHAR(40) NOT NULL,
        hours INT NULL,
        issued_at VARCHAR(10) NOT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'valido',
        revoked_at VARCHAR(19) NULL,
        revoked_reason VARCHAR(255) NULL,
        issued_by INT NULL,
        created_at VARCHAR(19) NOT NULL
    ){$tail}");
    $db->exec("CREATE TABLE IF NOT EXISTS certificate_checks (
        id {$id},
        code_tried VARCHAR(40) NOT NULL,
        found INT NOT NULL DEFAULT 0,
        ip VARCHAR(64) NULL,
        created_at VARCHAR(19) NOT NULL
    ){$tail}");
    // MySQL não aceita IF NOT EXISTS em índice; a migração roda uma única vez.
    $db->exec('CREATE INDEX idx_cert_checks_ip ON certificate_checks (ip, created_at)');
}

/** Gera 12 símbolos aleatórios (60 bits). */
function cert_generate_raw(): string {
    $out = '';
    for ($i = 0; $i < 12; $i++) {
        $out .= CERT_ALPHABET[random_int(0, 31)];
    }
    return $out;
}

function cert_format_code(string $raw): string {
    return CERT_PREFIX . '-' . implode('-', str_split($raw, 4));
}

/** Aceita "cbl 7k3q-9m2x-4tda" e devolve "CBL-7K3Q-9M2X-4TDA"; '' se o formato for inválido. */
function cert_normalize_code(string $input): string {
    $s = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $input) ?? '');
    if (str_starts_with($s, CERT_PREFIX)) {
        $s = substr($s, strlen(CERT_PREFIX));
    }
    if (!preg_match('/^[' . CERT_ALPHABET . ']{12}$/', $s)) {
        return '';
    }
    return cert_format_code($s);
}

function cert_verify_url(string $code): string {
    return rtrim((string)SITE_URL, '/') . '/verificar?c=' . rawurlencode($code);
}

/** Valida e normaliza os dados de um certificado. @return array{0:?array,1:?string} [dados, erro] */
function cert_validate(array $in): array {
    $name  = trim((string)($in['nome'] ?? ''));
    $event = trim((string)($in['evento'] ?? ''));
    $type  = strtolower(trim((string)($in['tipo'] ?? ''))) ?: 'participante';
    $email = trim((string)($in['email'] ?? ''));
    $hours = trim((string)($in['horas'] ?? ''));
    $date  = trim((string)($in['data'] ?? ''));

    if (mb_strlen($name, 'UTF-8') < 3 || mb_strlen($name, 'UTF-8') > 180) return [null, 'Nome deve ter de 3 a 180 caracteres.'];
    if (mb_strlen($event, 'UTF-8') < 3 || mb_strlen($event, 'UTF-8') > 220) return [null, 'Evento deve ter de 3 a 220 caracteres.'];
    if (!isset(cert_types()[$type])) return [null, 'Tipo inválido: ' . mb_substr($type, 0, 30, 'UTF-8') . '.'];
    if ($email !== '' && (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190)) return [null, 'E-mail inválido.'];
    if ($hours !== '' && (!ctype_digit($hours) || (int)$hours < 1 || (int)$hours > 9999)) return [null, 'Carga horária inválida.'];

    if ($date === '') {
        $date = date('Y-m-d');
    } elseif (preg_match('#^(\d{2})/(\d{2})/(\d{4})$#', $date, $m)) {
        $date = "{$m[3]}-{$m[2]}-{$m[1]}";
    }
    $d = DateTime::createFromFormat('Y-m-d', $date);
    if (!$d || $d->format('Y-m-d') !== $date) return [null, 'Data inválida (use AAAA-MM-DD ou DD/MM/AAAA).'];

    return [[
        'holder_name' => $name, 'holder_email' => $email ?: null, 'event_name' => $event,
        'cert_type' => $type, 'hours' => $hours === '' ? null : (int)$hours, 'issued_at' => $date,
    ], null];
}

/** Insere um certificado já validado e devolve a linha criada. */
function cert_insert(PDO $db, array $data, ?int $issuedBy): array {
    $stmt = $db->prepare(
        'INSERT INTO certificates (code, holder_name, holder_email, event_name, cert_type, hours, issued_at, issued_by, created_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    for ($try = 0; $try < 5; $try++) {
        $code = cert_format_code(cert_generate_raw());
        if (cert_find($db, $code)) {
            continue; // colisão (probabilidade desprezível)
        }
        $stmt->execute([$code, $data['holder_name'], $data['holder_email'], $data['event_name'], $data['cert_type'],
            $data['hours'], $data['issued_at'], $issuedBy, date('Y-m-d H:i:s')]);
        return cert_find($db, $code) ?? [];
    }
    throw new RuntimeException('Nao foi possivel gerar um codigo unico.');
}

function cert_find(PDO $db, string $code): ?array {
    $stmt = $db->prepare('SELECT * FROM certificates WHERE code = ? LIMIT 1');
    $stmt->execute([$code]);
    return $stmt->fetch() ?: null;
}

function cert_set_status(PDO $db, int $id, bool $revoke, string $reason = ''): void {
    if ($revoke) {
        $db->prepare("UPDATE certificates SET status = 'revogado', revoked_at = ?, revoked_reason = ? WHERE id = ?")
           ->execute([date('Y-m-d H:i:s'), mb_substr($reason, 0, 255, 'UTF-8') ?: null, $id]);
    } else {
        $db->prepare("UPDATE certificates SET status = 'valido', revoked_at = NULL, revoked_reason = NULL WHERE id = ?")->execute([$id]);
    }
}

/** Máximo de 30 consultas por IP em 10 minutos. */
function cert_check_rate_limited(PDO $db, string $ip): bool {
    $db->prepare('DELETE FROM certificate_checks WHERE created_at < ?')->execute([date('Y-m-d H:i:s', time() - 30 * 86400)]);
    $stmt = $db->prepare('SELECT COUNT(*) FROM certificate_checks WHERE ip = ? AND created_at > ?');
    $stmt->execute([$ip, date('Y-m-d H:i:s', time() - 600)]);
    return (int)$stmt->fetchColumn() >= 30;
}

function cert_log_check(PDO $db, string $codeTried, bool $found, string $ip): void {
    $db->prepare('INSERT INTO certificate_checks (code_tried, found, ip, created_at) VALUES (?, ?, ?, ?)')
       ->execute([mb_substr($codeTried, 0, 40, 'UTF-8'), $found ? 1 : 0, $ip, date('Y-m-d H:i:s')]);
}
