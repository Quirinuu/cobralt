<?php
/**
 * includes/layout.php
 * Layout compartilhado — header, nav e footer para todas as páginas públicas.
 */

define('INSTAGRAM_URL', 'https://www.instagram.com/cobralt_');

function layout_head(string $title, string $desc = '', string $base = '../', string $body_attrs = ''): void {
  layout_head_only($title, $desc, $base);
  echo '<body' . ($body_attrs ? ' ' . $body_attrs : '') . '>';
}

/** Emite apenas o bloco <head> sem a tag <body> — útil quando precisa de atributos no body */
function layout_head_only(string $title, string $desc = '', string $base = '../'): void { ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= htmlspecialchars($desc ?: 'CoBraLT — Comitê Brasileiro das Ligas do Trauma.') ?>">
  <meta name="theme-color" content="#013684">
  <title><?= htmlspecialchars($title) ?> — CoBraLT</title>
  <!-- Favicon -->
  <link rel="icon" type="image/png" href="<?= $base ?>assets/img/logo.png">
  <link rel="apple-touch-icon" href="<?= $base ?>assets/img/logo.png">
  <!-- Open Graph -->
  <meta property="og:title" content="<?= htmlspecialchars($title) ?> — CoBraLT">
  <meta property="og:description" content="<?= htmlspecialchars($desc ?: 'CoBraLT — Comitê Brasileiro das Ligas do Trauma.') ?>">
  <meta property="og:image" content="https://cobralT.org.br/assets/img/logo.png">
  <meta property="og:type" content="website">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= $base ?>css/style.css?v=6.2">
</head>
<?php }

function layout_header(string $active = '', string $base = '../', string $logo_href = '', string $nav_prefix = ''): void {
  $nav = [
    'noticias'   => ['href' => $nav_prefix . 'noticias.php',   'label' => 'Notícias'],
    'eventos'    => ['href' => $nav_prefix . 'eventos.php',    'label' => 'Eventos'],
    'projetos'   => ['href' => $nav_prefix . 'projetos.php',   'label' => 'Projetos'],
    'educacao'   => ['href' => $nav_prefix . 'educacao.php',   'label' => 'Educação'],
    'ligas'      => ['href' => $nav_prefix . 'ligas.php',      'label' => 'Ligas filiadas'],
    'apoiadores' => ['href' => $nav_prefix . 'apoiadores.php', 'label' => 'Apoiadores'],
    'historia'   => ['href' => $nav_prefix . 'historia.php',   'label' => 'História'],
    'diretoria'  => ['href' => $nav_prefix . 'diretoria.php',  'label' => 'Diretoria'],
  ];
  $ig = INSTAGRAM_URL;
?>
<header class="site-header" role="banner">
  <div class="header-inner">
    <a href="<?= $logo_href ?: $base . 'index.php' ?>" class="logo has-image" aria-label="CoBraLT — Início">
      <img src="<?= $base ?>assets/img/logo.png?v=6.2" alt="CoBraLT">
    </a>
    <nav class="nav-desktop" aria-label="Navegação principal">
      <?php foreach ($nav as $key => $item): ?>
      <a href="<?= $item['href'] ?>"<?= $active === $key ? ' class="active"' : '' ?>><?= $item['label'] ?></a>
      <?php endforeach; ?>
      <a href="<?= $ig ?>" target="_blank" rel="noopener noreferrer" class="nav-instagram" aria-label="Instagram do CoBraLT">
        <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
      </a>
      <a href="<?= $nav_prefix ?>login.php" class="nav-login"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>Admin</a>
    </nav>
    <button class="hamburger" id="hamburger" aria-label="Abrir menu" aria-expanded="false">
      <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
  </div>
</header>
<div class="drawer-overlay" id="drawerOverlay" aria-hidden="true"></div>
<aside class="mobile-drawer" id="mobileDrawer" role="dialog" aria-modal="true">
  <div class="drawer-header">
    <span class="drawer-header-logo">CoBraLT</span>
    <button class="drawer-close" id="drawerClose" aria-label="Fechar menu">
      <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
  </div>
  <nav class="drawer-nav">
    <?php foreach ($nav as $key => $item): ?>
    <a href="<?= $item['href'] ?>"<?= $active === $key ? ' class="active"' : '' ?>><?= $item['label'] ?></a>
    <?php endforeach; ?>
    <a href="<?= $ig ?>" target="_blank" rel="noopener noreferrer" style="display:flex;align-items:center;gap:8px;">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
      Instagram
    </a>
    <a href="<?= $nav_prefix ?>login.php" class="nav-login">Admin</a>
  </nav>
</aside>
<?php }

function layout_footer(string $base = '../', array $extra_scripts = []): void {
  $ig = INSTAGRAM_URL;
?>
<footer class="site-footer" role="contentinfo">
  <div class="footer-inner">
    <div class="footer-top">
      <div class="footer-brand">
        <div class="logo" style="margin-bottom:1rem;">
          <div class="logo-text"><span class="logo-name">CoBraLT</span><span class="logo-tagline">Ligas do Trauma</span></div>
        </div>
        <p>Conectando profissionais de saúde de todo o Brasil em torno da excelência no atendimento ao trauma.</p>
        <div class="social-links" style="margin-top:1rem;">
          <a href="<?= $ig ?>" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="Instagram do CoBraLT">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
          </a>
        </div>
      </div>
      <div class="footer-col">
        <h4>Conteúdo</h4>
        <a href="noticias.php">Notícias</a>
        <a href="eventos.php">Eventos</a>
        <a href="projetos.php">Projetos</a>
        <a href="educacao.php">Educação</a>
      </div>
      <div class="footer-col">
        <h4>CoBraLT</h4>
        <a href="ligas.php">Ligas filiadas</a>
        <a href="diretoria.php">Diretoria</a>
        <a href="historia.php">História</a>
        <a href="<?= $base ?>index.php#filiacao">Filiar minha liga</a>
      </div>
      <div class="footer-col">
        <h4>Legal</h4>
        <a href="privacidade.php">Política de Privacidade</a>
        <a href="termos.php">Termos de Uso</a>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© 2026 Comitê Brasileiro das Ligas do Trauma — CoBraLT. Todos os direitos reservados. <span style="opacity:.4;font-size:.78em;">Dev Gabriel Quirino</span></p>
      <div style="display:flex;gap:1.5rem;">
        <a href="privacidade.php">Privacidade</a>
        <a href="termos.php">Termos de Uso</a>
      </div>
    </div>
  </div>
</footer>
<script src="<?= $base ?>js/main.js?v=6.2"></script>
<script src="<?= $base ?>js/animations.js?v=6.2"></script>
<?php foreach ($extra_scripts as $s): ?>
<script src="<?= $base ?><?= $s ?>?v=6.2"></script>
<?php endforeach; ?>
</body>
</html>
<?php }
