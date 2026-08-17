<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/includes/supporters.php';

return [
    'id' => '2026081701_supporters_admin',
    'description' => 'Cria o cadastro administrativo de apoiadores e importa os cards existentes.',
    'up' => static function (PDO $db): void {
        $driver = (string)$db->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver === 'sqlite') {
            $db->exec(
                'CREATE TABLE IF NOT EXISTS apoiadores (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    nome TEXT NOT NULL,
                    instituicao TEXT NULL,
                    imagem TEXT NOT NULL,
                    ativo INTEGER NOT NULL DEFAULT 1,
                    ordem INTEGER NOT NULL DEFAULT 0,
                    created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
                )'
            );
            $db->exec('CREATE INDEX IF NOT EXISTS idx_apoiadores_ativo_ordem ON apoiadores(ativo, ordem, nome)');
        } else {
            $db->exec(
                "CREATE TABLE IF NOT EXISTS apoiadores (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    nome VARCHAR(180) NOT NULL,
                    instituicao VARCHAR(255) NULL,
                    imagem VARCHAR(500) NOT NULL,
                    ativo TINYINT(1) NOT NULL DEFAULT 1,
                    ordem INT NOT NULL DEFAULT 0,
                    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    INDEX idx_apoiadores_ativo_ordem (ativo, ordem, nome)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            );
        }

        if ((int)$db->query('SELECT COUNT(*) FROM apoiadores')->fetchColumn() > 0) {
            return;
        }

        $insert = $db->prepare(
            'INSERT INTO apoiadores (nome, instituicao, imagem, ativo, ordem)
             VALUES (:nome, :instituicao, :imagem, :ativo, :ordem)'
        );
        foreach (supporters_default_data() as $supporter) {
            $insert->execute($supporter);
        }
    },
];
