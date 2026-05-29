<?php
/**
 * includes/db.php
 * Shared PDO connection for public pages and the admin panel.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config.php';

function getPublicDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = db_connect();
        db_ensure_schema($pdo);
    }
    return $pdo;
}

function db_connect(): PDO {
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $dsn = defined('DB_DSN') ? (string)DB_DSN : '';
    $driver = db_config_driver();

    if ($dsn !== '') {
        return new PDO($dsn, DB_USER, DB_PASS, $options);
    }

    if ($driver === 'sqlite') {
        $sqlitePath = defined('SQLITE_PATH') ? (string)SQLITE_PATH : dirname(__DIR__) . '/storage/cobralt.sqlite';
        $storageDir = dirname($sqlitePath);
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }
        return new PDO('sqlite:' . $sqlitePath, null, null, $options);
    }

    return new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        $options
    );
}

function db_config_driver(): string {
    if (defined('DB_DRIVER')) {
        return (string)DB_DRIVER;
    }

    // Compatibilidade com o config.php antigo da Hostinger, que define apenas
    // DB_HOST/DB_NAME/DB_USER/DB_PASS. Se houver DB_NAME, o site deve usar MySQL.
    return defined('DB_NAME') && (string)DB_NAME !== '' ? 'mysql' : 'sqlite';
}

function db_driver(PDO $pdo): string {
    return (string)$pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
}

function db_ensure_schema(PDO $pdo): void {
    static $done = [];
    $key = spl_object_id($pdo);
    if (isset($done[$key])) {
        return;
    }

    try {
        if (db_driver($pdo) === 'sqlite') {
            db_create_sqlite_schema($pdo);
            db_migrate_sqlite_schema($pdo);
        } else {
            db_create_mysql_schema($pdo);
            db_migrate_mysql_schema($pdo);
        }
    } catch (Throwable $e) {
        error_log('[CoBraLT] schema check failed: ' . $e->getMessage());
    }

    $done[$key] = true;
}

function db_create_mysql_schema(PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admin_users (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(60) NOT NULL UNIQUE,
            email VARCHAR(190) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            role ENUM('superadmin','admin','editor') NOT NULL DEFAULT 'editor',
            active TINYINT(1) NOT NULL DEFAULT 1,
            last_login DATETIME NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS posts (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            author_id INT UNSIGNED NULL,
            title VARCHAR(220) NOT NULL,
            slug VARCHAR(240) NOT NULL UNIQUE,
            tipo ENUM('noticias','eventos','projetos','educacao') NOT NULL DEFAULT 'noticias',
            excerpt TEXT NULL,
            content MEDIUMTEXT NOT NULL,
            cover_image VARCHAR(500) NULL,
            category VARCHAR(100) NULL,
            status ENUM('draft','published') NOT NULL DEFAULT 'draft',
            published_at DATETIME NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_posts_status_tipo (status, tipo),
            INDEX idx_posts_author (author_id),
            CONSTRAINT fk_posts_author FOREIGN KEY (author_id) REFERENCES admin_users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS pages (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            author_id INT UNSIGNED NULL,
            title VARCHAR(220) NOT NULL,
            slug VARCHAR(240) NOT NULL UNIQUE,
            content MEDIUMTEXT NOT NULL,
            status ENUM('draft','published') NOT NULL DEFAULT 'draft',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_pages_status (status),
            INDEX idx_pages_author (author_id),
            CONSTRAINT fk_pages_author FOREIGN KEY (author_id) REFERENCES admin_users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS eventos (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            dia VARCHAR(20) NOT NULL,
            mes VARCHAR(20) NOT NULL,
            titulo VARCHAR(220) NOT NULL,
            local VARCHAR(220) NOT NULL DEFAULT '',
            descricao TEXT NULL,
            link VARCHAR(500) NULL,
            link_texto VARCHAR(80) NOT NULL DEFAULT 'Saiba mais',
            link_externo TINYINT(1) NOT NULL DEFAULT 0,
            ativo TINYINT(1) NOT NULL DEFAULT 1,
            ordem INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_eventos_ativo_ordem (ativo, ordem)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS ligas_regioes (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            emoji VARCHAR(20) NOT NULL DEFAULT '',
            nome VARCHAR(160) NOT NULL,
            descricao TEXT NULL,
            contagem INT NOT NULL DEFAULT 0,
            link VARCHAR(500) NULL,
            ativo TINYINT(1) NOT NULL DEFAULT 1,
            ordem INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_ligas_ativo_ordem (ativo, ordem)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS diretoria (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(160) NOT NULL,
            cargo VARCHAR(160) NOT NULL DEFAULT '',
            especialidade VARCHAR(220) NULL,
            foto VARCHAR(500) NULL,
            grupo VARCHAR(80) NOT NULL DEFAULT 'Diretoria',
            bio TEXT NULL,
            ativo TINYINT(1) NOT NULL DEFAULT 1,
            ordem INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_diretoria_ativo_ordem (ativo, grupo, ordem)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS form_submissions (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            type VARCHAR(40) NOT NULL,
            payload_json MEDIUMTEXT NOT NULL,
            ip VARCHAR(80) NULL,
            user_agent VARCHAR(255) NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_form_submissions_type_created (type, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
}

function db_create_sqlite_schema(PDO $pdo): void {
    $pdo->exec('PRAGMA foreign_keys = ON');

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admin_users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            email TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            role TEXT NOT NULL DEFAULT 'editor' CHECK(role IN ('superadmin','admin','editor')),
            active INTEGER NOT NULL DEFAULT 1,
            last_login TEXT NULL,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )
    ");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS posts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            author_id INTEGER NULL,
            title TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            tipo TEXT NOT NULL DEFAULT 'noticias' CHECK(tipo IN ('noticias','eventos','projetos','educacao')),
            excerpt TEXT NULL,
            content TEXT NOT NULL,
            cover_image TEXT NULL,
            category TEXT NULL,
            status TEXT NOT NULL DEFAULT 'draft' CHECK(status IN ('draft','published')),
            published_at TEXT NULL,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (author_id) REFERENCES admin_users(id) ON DELETE SET NULL
        )
    ");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_posts_status_tipo ON posts(status, tipo)");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS pages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            author_id INTEGER NULL,
            title TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            content TEXT NOT NULL,
            status TEXT NOT NULL DEFAULT 'draft' CHECK(status IN ('draft','published')),
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (author_id) REFERENCES admin_users(id) ON DELETE SET NULL
        )
    ");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_pages_status ON pages(status)");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS eventos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            dia TEXT NOT NULL,
            mes TEXT NOT NULL,
            titulo TEXT NOT NULL,
            local TEXT NOT NULL DEFAULT '',
            descricao TEXT NULL,
            link TEXT NULL,
            link_texto TEXT NOT NULL DEFAULT 'Saiba mais',
            link_externo INTEGER NOT NULL DEFAULT 0,
            ativo INTEGER NOT NULL DEFAULT 1,
            ordem INTEGER NOT NULL DEFAULT 0,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )
    ");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_eventos_ativo_ordem ON eventos(ativo, ordem)");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS ligas_regioes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            emoji TEXT NOT NULL DEFAULT '',
            nome TEXT NOT NULL,
            descricao TEXT NULL,
            contagem INTEGER NOT NULL DEFAULT 0,
            link TEXT NULL,
            ativo INTEGER NOT NULL DEFAULT 1,
            ordem INTEGER NOT NULL DEFAULT 0,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )
    ");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_ligas_ativo_ordem ON ligas_regioes(ativo, ordem)");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS diretoria (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nome TEXT NOT NULL,
            cargo TEXT NOT NULL DEFAULT '',
            especialidade TEXT NULL,
            foto TEXT NULL,
            grupo TEXT NOT NULL DEFAULT 'Diretoria',
            bio TEXT NULL,
            ativo INTEGER NOT NULL DEFAULT 1,
            ordem INTEGER NOT NULL DEFAULT 0,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )
    ");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_diretoria_ativo_ordem ON diretoria(ativo, grupo, ordem)");

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS form_submissions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            type TEXT NOT NULL,
            payload_json TEXT NOT NULL,
            ip TEXT NULL,
            user_agent TEXT NULL,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )
    ");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_form_submissions_type_created ON form_submissions(type, created_at)");
}

function db_migrate_mysql_schema(PDO $pdo): void {
    db_add_column_if_missing($pdo, 'posts', 'tipo', "ALTER TABLE posts ADD COLUMN tipo VARCHAR(30) NOT NULL DEFAULT 'noticias' AFTER slug");
    db_add_column_if_missing($pdo, 'posts', 'updated_at', "ALTER TABLE posts ADD COLUMN updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    db_add_column_if_missing($pdo, 'pages', 'updated_at', "ALTER TABLE pages ADD COLUMN updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
}

function db_migrate_sqlite_schema(PDO $pdo): void {
    db_add_column_if_missing($pdo, 'posts', 'tipo', "ALTER TABLE posts ADD COLUMN tipo TEXT NOT NULL DEFAULT 'noticias'");
    db_add_column_if_missing($pdo, 'posts', 'updated_at', "ALTER TABLE posts ADD COLUMN updated_at TEXT NULL");
    db_add_column_if_missing($pdo, 'pages', 'updated_at', "ALTER TABLE pages ADD COLUMN updated_at TEXT NULL");
}

function db_add_column_if_missing(PDO $pdo, string $table, string $column, string $sql): void {
    if (!db_column_exists($pdo, $table, $column)) {
        $pdo->exec($sql);
    }
}

function db_column_exists(PDO $pdo, string $table, string $column): bool {
    if (db_driver($pdo) === 'sqlite') {
        $stmt = $pdo->query('PRAGMA table_info(' . $table . ')');
        foreach ($stmt->fetchAll() as $row) {
            if (($row['name'] ?? '') === $column) {
                return true;
            }
        }
        return false;
    }

    $stmt = $pdo->prepare("SHOW COLUMNS FROM `{$table}` LIKE ?");
    $stmt->execute([$column]);
    return (bool)$stmt->fetch();
}

/** Escapa output para HTML */
function h(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
