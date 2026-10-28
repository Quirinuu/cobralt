<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/certificates.php';

return [
    'id' => '2026102801_certificates',
    'description' => 'Cria as tabelas do autenticador de certificados.',
    'up' => static function (PDO $db): void {
        cert_create_tables($db);
    },
];
