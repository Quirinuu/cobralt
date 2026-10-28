<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/certificates.php';

return [
    'id' => '2026102802_certificate_templates',
    'description' => 'Modelos de certificado, assinantes e hash do PDF emitido.',
    'up' => static function (PDO $db): void {
        cert_upgrade_tables_v2($db);
    },
];
