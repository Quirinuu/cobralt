<?php
/**
 * admin/_layout.php
 * Layout único do painel: menu lateral, estilos e estrutura de página.
 * Para criar uma página nova, basta:
 *   require_once __DIR__ . '/_auth.php';
 *   admin_page_start('Título', 'chave-do-menu', 'Subtítulo opcional');
 *   ... conteúdo ...
 *   admin_page_end();
 * Para um item novo no menu, edite SOMENTE admin_menu() abaixo.
 */

declare(strict_types=1);

/** Itens do menu: [grupo => [[chave, rótulo, arquivo, perfis permitidos (vazio = todos), ícone]]] */
function admin_menu(): array {
    $icons = [
        'grid'   => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>',
        'doc'    => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
        'edit'   => '<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>',
        'page'   => '<path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/>',
        'users'  => '<path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6M23 11h-6"/>',
        'mail'   => '<path d="M4 4h16v16H4z"/><polyline points="4 8 12 13 20 8"/>',
        'award'  => '<circle cx="12" cy="9" r="6"/><path d="M8.5 14L7 22l5-3 5 3-1.5-8"/>',
        'pen'    => '<path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4z"/>',
        'shield' => '<path d="M12 2l8 4v6c0 5-3.5 8-8 10-4.5-2-8-5-8-10V6z"/><polyline points="9 12 11 14 15 10"/>',
        'layout' => '<rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/>',
        'user'   => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'ext'    => '<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>',
    ];
    $adm = ['superadmin', 'admin'];
    return [
        'Geral' => [
            ['dashboard', 'Dashboard', 'dashboard.php', [], $icons['grid']],
        ],
        'Conteúdo' => [
            ['posts', 'Notícias', 'posts.php', [], $icons['doc']],
            ['post-editor', 'Nova notícia', 'post-editor.php', [], $icons['edit']],
            ['pages', 'Páginas', 'pages.php', $adm, $icons['page']],
            ['apoiadores', 'Apoiadores', 'apoiadores.php', [], $icons['users']],
            ['formularios', 'Formulários', 'formularios.php', $adm, $icons['mail']],
        ],
        'Certificados' => [
            ['certificados', 'Emitir e listar', 'certificados.php', $adm, $icons['award']],
            ['assinar-pdf', 'Assinar PDF pronto', 'certificado-assinar-pdf.php', $adm, $icons['pen']],
            ['autenticar', 'Autenticar', 'autenticar.php', $adm, $icons['shield']],
            ['modelos', 'Modelos e assinaturas', 'certificado-modelos.php', $adm, $icons['layout']],
        ],
        'Sistema' => [
            ['usuarios', 'Usuários', 'usuarios.php', ['superadmin'], $icons['user']],
            ['site', 'Ver site', '../index.php', [], $icons['ext']],
        ],
    ];
}

function admin_shared_css(): void {
    echo '<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">' . "\n";
    echo '<style>' . (string)file_get_contents(__DIR__ . '/_layout.css') . '</style>' . "\n";
}

function admin_sidebar(string $active = ''): void {
    global $adminUser, $adminRole;
    ?>
<button type="button" class="menu-toggle" aria-label="Abrir menu" onclick="document.querySelector('.sidebar').classList.toggle('open')">☰</button>
<aside class="sidebar">
  <div class="sidebar-header">
    <div class="sidebar-logo-mark"><svg viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg></div>
    <div class="sidebar-brand"><h1>CoBraLT</h1><p>Painel Admin</p></div>
  </div>
  <nav class="sidebar-nav">
    <?php foreach (admin_menu() as $group => $items):
        $visible = array_filter($items, static fn($i) => !$i[3] || in_array($adminRole, $i[3], true));
        if (!$visible) continue; ?>
      <div class="nav-label"><?= e($group) ?></div>
      <?php foreach ($visible as [$key, $label, $href, , $icon]): ?>
        <a href="<?= e($href) ?>"<?= $key === $active ? ' class="active"' : '' ?><?= $key === 'site' ? ' target="_blank" rel="noopener"' : '' ?>>
          <svg viewBox="0 0 24 24"><?= $icon /* SVG fixo definido acima */ ?></svg><?= e($label) ?>
        </a>
      <?php endforeach; ?>
    <?php endforeach; ?>
  </nav>
  <div class="sidebar-footer">
    <div class="user-info">
      <div class="user-avatar"><?= e(strtoupper(substr((string)$adminUser, 0, 1))) ?></div>
      <div><div class="user-name"><?= e((string)$adminUser) ?></div><div class="user-role"><?= e((string)$adminRole) ?></div></div>
    </div>
    <a href="../api/logout.php" class="btn-logout">
      <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>Sair
    </a>
  </div>
</aside>
<?php
}

/** Abre a página: <head>, menu lateral, barra superior e área de conteúdo. */
function admin_page_start(string $title, string $active, string $subtitle = '', string $topbarExtra = ''): void {
    ?><!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title><?= e($title) ?> — Admin CoBraLT</title>
  <?php admin_shared_css(); ?>
</head>
<body>
<?php admin_sidebar($active); ?>
<div class="main">
  <div class="topbar">
    <div><h2><?= e($title) ?></h2><?php if ($subtitle): ?><span class="topbar-sub"><?= e($subtitle) ?></span><?php endif; ?></div>
    <div class="topbar-actions"><?= $topbarExtra /* HTML montado pela própria página */ ?></div>
  </div>
  <div class="content">
<?php
}

function admin_page_end(string $scripts = ''): void {
    echo "  </div>\n</div>\n" . $scripts . "\n</body>\n</html>\n";
}

/** Alertas padronizados. */
function admin_alerts(string $msg = '', array $errors = []): void {
    if ($msg !== '') echo '<div class="alert ok">' . e($msg) . '</div>';
    foreach ($errors as $er) echo '<div class="alert err">' . e((string)$er) . '</div>';
}

/**
 * Erro inesperado no painel: mostra o motivo ao admin logado (em vez de uma tela 500 em branco) e registra no log.
 * Só é registrado depois de a autenticação passar, então visitantes nunca veem estas mensagens.
 */
set_exception_handler(static function (Throwable $err): void {
    error_log('[CoBraLT admin] ' . get_class($err) . ': ' . $err->getMessage() . ' @ ' . $err->getFile() . ':' . $err->getLine());
    if (!headers_sent()) {
        http_response_code(500);
    }
    $detail = get_class($err) . ': ' . $err->getMessage() . ' (' . basename($err->getFile()) . ':' . $err->getLine() . ')';
    if (admin_auth_is_api_request()) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => $detail], JSON_UNESCAPED_UNICODE);
        return;
    }
    echo '<!DOCTYPE html><html lang="pt-BR"><meta charset="UTF-8"><title>Erro — Admin CoBraLT</title>'
        . '<body style="font-family:Segoe UI,sans-serif;max-width:720px;margin:3rem auto;padding:0 1rem;color:#1E293B">'
        . '<h1 style="color:#B91C1C;font-size:1.3rem">Ocorreu um erro nesta página</h1>'
        . '<p>Detalhe técnico (envie ao desenvolvedor):</p>'
        . '<pre style="white-space:pre-wrap;background:#F1F5F9;border:1px solid #E2E8F0;border-radius:8px;padding:1rem">' . e($detail) . '</pre>'
        . '<p><a href="dashboard.php">← Voltar ao painel</a></p></body></html>';
});
