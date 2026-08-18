<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/directors.php';

return [
    'id' => '2026081703_seed_empty_directors',
    'description' => 'Restaura a Diretoria CoBraLT quando a tabela estiver vazia.',
    'up' => static function (PDO $db): void {
        directors_seed_if_empty($db);
    },
];
