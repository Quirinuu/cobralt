<?php
/**
 * config.php — Configurações centrais do CoBraLT
 * Coloque na RAIZ do site (mesmo nível de index.html)
 * NUNCA versione este arquivo com senhas reais.
 */

// ─── BANCO DE DADOS ───────────────────────────────────────────────────────────
// Preencha com os dados do painel da Hostinger → Banco de Dados → MySQL
define('DB_HOST', 'localhost');
define('DB_NAME', 'u464752963_NewSiteCobralt');     // ex: u123456789_cobralT
define('DB_USER', 'u464752963_NewSite');  // ex: u123456789_admin
define('DB_PASS', '>F9m;WsOURY');

// ─── UPLOADS ──────────────────────────────────────────────────────────────────
define('UPLOAD_DIR', __DIR__ . '/assets/img/uploads/');
define('UPLOAD_URL', '/assets/img/uploads/');
define('UPLOAD_MAX_MB', 5);

// ─── SITE ─────────────────────────────────────────────────────────────────────
define('SITE_NAME', 'CoBraLT');
define('SITE_URL', 'https://cobralT.org.br'); // ← troque pelo seu domínio real (sem barra no final)
