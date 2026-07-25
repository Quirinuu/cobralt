<?php
/**
 * Executor de migrações versionadas do CoBraLT.
 *
 * Cada arquivo em database/migrations deve retornar:
 * [
 *   'id' => 'AAAAMMDDNN_descricao',
 *   'description' => 'Descrição curta',
 *   'up' => static function (PDO $db): void { ... },
 * ]
 */

declare(strict_types=1);

function db_migrations_directory(): string {
    return dirname(__DIR__) . '/database/migrations';
}

function db_ensure_migrations_table(PDO $db): void {
    if ((string)$db->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
        $db->exec(
            'CREATE TABLE IF NOT EXISTS schema_migrations (
                migration TEXT PRIMARY KEY,
                description TEXT NOT NULL,
                checksum TEXT NOT NULL,
                executed_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )'
        );
        return;
    }

    $db->exec(
        "CREATE TABLE IF NOT EXISTS schema_migrations (
            migration VARCHAR(190) NOT NULL PRIMARY KEY,
            description VARCHAR(255) NOT NULL,
            checksum CHAR(64) NOT NULL,
            executed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
}

/**
 * @return array<int,array{id:string,description:string,up:callable,file:string,checksum:string}>
 */
function db_discover_migrations(): array {
    $directory = db_migrations_directory();
    if (!is_dir($directory)) {
        return [];
    }

    $files = glob($directory . '/*.php') ?: [];
    sort($files, SORT_STRING);
    $migrations = [];
    $knownIds = [];

    foreach ($files as $file) {
        $definition = require $file;
        if (
            !is_array($definition)
            || empty($definition['id'])
            || empty($definition['description'])
            || !isset($definition['up'])
            || !is_callable($definition['up'])
        ) {
            throw new RuntimeException('Migração inválida: ' . basename($file));
        }

        $id = (string)$definition['id'];
        if (!preg_match('/^[0-9]{10}_[a-z0-9_]+$/', $id)) {
            throw new RuntimeException('Identificador inválido na migração: ' . basename($file));
        }
        if (isset($knownIds[$id])) {
            throw new RuntimeException('Migração duplicada: ' . $id);
        }

        $knownIds[$id] = true;
        $migrations[] = [
            'id' => $id,
            'description' => (string)$definition['description'],
            'up' => $definition['up'],
            'file' => $file,
            'checksum' => hash_file('sha256', $file),
        ];
    }

    return $migrations;
}

/**
 * @return array<string,array{description:string,checksum:string,executed_at:string}>
 */
function db_applied_migrations(PDO $db): array {
    db_ensure_migrations_table($db);
    $rows = $db->query(
        'SELECT migration, description, checksum, executed_at
         FROM schema_migrations
         ORDER BY migration'
    )->fetchAll();
    $applied = [];

    foreach ($rows as $row) {
        $applied[(string)$row['migration']] = [
            'description' => (string)$row['description'],
            'checksum' => (string)$row['checksum'],
            'executed_at' => (string)$row['executed_at'],
        ];
    }

    return $applied;
}

/**
 * @return array<int,array{id:string,description:string,status:string,executed_at:?string}>
 */
function db_migration_status(PDO $db): array {
    $applied = db_applied_migrations($db);
    $status = [];

    foreach (db_discover_migrations() as $migration) {
        $record = $applied[$migration['id']] ?? null;
        $migrationStatus = 'pending';
        if ($record) {
            $migrationStatus = hash_equals($record['checksum'], $migration['checksum'])
                ? 'applied'
                : 'changed';
        }

        $status[] = [
            'id' => $migration['id'],
            'description' => $migration['description'],
            'status' => $migrationStatus,
            'executed_at' => $record['executed_at'] ?? null,
        ];
    }

    return $status;
}

/**
 * Executa apenas migrações ainda não registradas.
 *
 * @return string[] IDs aplicados nesta execução.
 */
function db_run_pending_migrations(PDO $db): array {
    db_ensure_migrations_table($db);
    $driver = (string)$db->getAttribute(PDO::ATTR_DRIVER_NAME);
    $lockAcquired = false;

    if ($driver === 'mysql') {
        $lockStmt = $db->query("SELECT GET_LOCK('cobralt_schema_migrations', 10)");
        $lockAcquired = (int)$lockStmt->fetchColumn() === 1;
        if (!$lockAcquired) {
            throw new RuntimeException('Não foi possível obter o bloqueio das migrações.');
        }
    }

    try {
        $applied = db_applied_migrations($db);
        $executed = [];

        foreach (db_discover_migrations() as $migration) {
            if (isset($applied[$migration['id']])) {
                if (!hash_equals($applied[$migration['id']]['checksum'], $migration['checksum'])) {
                    error_log('[CoBraLT] Migração já aplicada foi alterada: ' . $migration['id']);
                }
                continue;
            }

            $startedTransaction = false;
            try {
                if (!$db->inTransaction()) {
                    $db->beginTransaction();
                    $startedTransaction = true;
                }

                ($migration['up'])($db);

                $record = $db->prepare(
                    'INSERT INTO schema_migrations (migration, description, checksum)
                     VALUES (?, ?, ?)'
                );
                $record->execute([
                    $migration['id'],
                    $migration['description'],
                    $migration['checksum'],
                ]);

                if ($startedTransaction && $db->inTransaction()) {
                    $db->commit();
                }
                $executed[] = $migration['id'];
            } catch (Throwable $e) {
                if ($startedTransaction && $db->inTransaction()) {
                    $db->rollBack();
                }
                throw new RuntimeException(
                    'Falha na migração ' . $migration['id'] . ': ' . $e->getMessage(),
                    0,
                    $e
                );
            }
        }

        return $executed;
    } finally {
        if ($driver === 'mysql' && $lockAcquired) {
            $db->query("SELECT RELEASE_LOCK('cobralt_schema_migrations')");
        }
    }
}
