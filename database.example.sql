-- CoBraLT database schema (MySQL/MariaDB)
-- Import this file in phpMyAdmin, then create at least one admin user.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS form_submissions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  type VARCHAR(40) NOT NULL,
  payload_json MEDIUMTEXT NOT NULL,
  ip VARCHAR(80) NULL,
  user_agent VARCHAR(255) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_form_submissions_type_created (type, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- Example: create an admin hash with:
-- php -r "echo password_hash('SuaSenhaForte!123', PASSWORD_BCRYPT, ['cost'=>12]);"
-- Then run:
-- INSERT INTO admin_users (username, email, password_hash, role)
-- VALUES ('admin', 'admin@cobralt.org.br', 'HASH_GERADO', 'superadmin');
