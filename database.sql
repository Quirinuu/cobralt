-- ═══════════════════════════════════════════════════════════
--  CoBraLT — Banco de dados
--  Execute no phpMyAdmin da Hostinger
--  Substitua `cobralT_db` pelo nome real do seu banco
-- ═══════════════════════════════════════════════════════════

SET NAMES utf8mb4;
SET time_zone = '-03:00';

-- ─── USUÁRIOS ADMIN ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id`            INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `username`      VARCHAR(60)      NOT NULL UNIQUE,
  `email`         VARCHAR(120)     NOT NULL UNIQUE,
  `password_hash` VARCHAR(255)     NOT NULL,
  `role`          ENUM('superadmin','admin','editor') NOT NULL DEFAULT 'editor',
  `active`        TINYINT(1)       NOT NULL DEFAULT 1,
  `last_login`    DATETIME         NULL,
  `created_at`    DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── POSTS / NOTÍCIAS ─────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `posts` (
  `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `author_id`   INT UNSIGNED     NOT NULL,
  `title`       VARCHAR(255)     NOT NULL,
  `slug`        VARCHAR(255)     NOT NULL UNIQUE,
  `excerpt`     TEXT             NULL,
  `content`     LONGTEXT         NOT NULL,
  `cover_image` VARCHAR(500)     NULL,
  `category`    VARCHAR(80)      NULL,
  `status`      ENUM('draft','published') NOT NULL DEFAULT 'draft',
  `published_at` DATETIME        NULL,
  `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_published_at` (`published_at`),
  FOREIGN KEY (`author_id`) REFERENCES `admin_users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── PÁGINAS ESTÁTICAS ────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `pages` (
  `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `author_id`   INT UNSIGNED     NOT NULL,
  `title`       VARCHAR(255)     NOT NULL,
  `slug`        VARCHAR(255)     NOT NULL UNIQUE,
  `content`     LONGTEXT         NOT NULL,
  `status`      ENUM('draft','published') NOT NULL DEFAULT 'draft',
  `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`author_id`) REFERENCES `admin_users`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── INSCRIÇÕES NO COLT ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS `inscricoes_colt` (
  `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `nome`        VARCHAR(150)     NOT NULL,
  `email`       VARCHAR(120)     NOT NULL,
  `profissao`   VARCHAR(80)      NOT NULL,
  `estado`      CHAR(2)          NOT NULL,
  `instituicao` VARCHAR(200)     NULL,
  `ip`          VARCHAR(45)      NULL,
  `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ─── FILIAÇÕES ────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS `filiacao_solicitacoes` (
  `id`          INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  `instituicao` VARCHAR(200)     NOT NULL,
  `estado`      VARCHAR(10)      NOT NULL,
  `responsavel` VARCHAR(150)     NOT NULL,
  `email`       VARCHAR(120)     NOT NULL,
  `telefone`    VARCHAR(30)      NOT NULL,
  `cnpj`        VARCHAR(20)      NOT NULL,
  `status`      ENUM('pendente','aprovada','rejeitada') NOT NULL DEFAULT 'pendente',
  `ip`          VARCHAR(45)      NULL,
  `created_at`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ═══════════════════════════════════════════════════════════
--  CRIAR O PRIMEIRO USUÁRIO ADMIN
--
--  Senha: Mude123!@# (troque ANTES de rodar em produção)
--  Para gerar um hash novo, use:
--    php -r "echo password_hash('SuaSenhaAqui', PASSWORD_BCRYPT, ['cost'=>12]);"
-- ═══════════════════════════════════════════════════════════
INSERT INTO `admin_users` (`username`, `email`, `password_hash`, `role`) VALUES
(
  'admin',
  'admin@cobralT.org.br',
  '$2y$12$placeholder_troque_este_hash_agora_mesmo_antes_de_usar',
  'superadmin'
);

-- Para gerar um hash real, rode no terminal da Hostinger:
-- php -r "echo password_hash('SuaSenhaForte!123', PASSWORD_BCRYPT, ['cost'=>12]);"
-- Depois atualize com:
-- UPDATE admin_users SET password_hash = 'HASH_GERADO_AQUI' WHERE username = 'admin';
