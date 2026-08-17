<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/supporters.php';

return [
    'id' => '2026081702_seed_empty_supporters',
    'description' => 'Preenche apoiadores quando a tabela da hospedagem foi criada vazia.',
    'up' => static function (PDO $db): void {
        supporters_seed_if_empty($db);
    },
];
