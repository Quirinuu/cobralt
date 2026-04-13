<?php
require_once __DIR__ . '/_auth.php';

$db = getDB();

// Estatísticas rápidas
$totalPosts  = $db->query('SELECT COUNT(*) FROM posts')->fetchColumn();
$pubPosts    = $db->query('SELECT COUNT(*) FROM posts WHERE status = "published"')->fetchColumn();
$totalPages  = $db->query('SELECT COUNT(*) FROM pages')->fetchColumn();
$recentPosts = $db->query('SELECT id, title, status, created_at FROM posts ORDER BY created_at DESC LIMIT 8')->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — Admin CoBraLT</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --navy:#002B4E; --sky:#0EA5E9; --sky-dark:#0284C7;
      --success:#10B981; --danger:#EF4444; --warning:#F59E0B;
      --slate-100:#F1F5F9; --slate-200:#E2E8F0; --slate-400:#94A3B8;
      --slate-600:#475569; --slate-800:#1E293B;
      --font-display:'Playfair Display',Georgia,serif;
      --font-body:'DM Sans','Segoe UI',sans-serif;
      --sidebar-w:240px;
    }
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:var(--font-body);background:var(--slate-100);color:var(--slate-800);display:flex;min-height:100vh;}

    /* ─── SIDEBAR ─────────────────────────────────────── */
    .sidebar {
      width:var(--sidebar-w);
      background:var(--navy);
      color:#fff;
      display:flex;
      flex-direction:column;
      position:fixed;
      top:0;bottom:0;left:0;
      z-index:100;
      overflow-y:auto;
    }

    .sidebar-header {
      padding:1.5rem 1.25rem 1rem;
      border-bottom:1px solid rgba(255,255,255,0.08);
      display:flex;align-items:center;gap:10px;
    }

    .sidebar-logo-mark {
      width:36px;height:36px;
      background:rgba(14,165,233,0.25);
      border-radius:8px;
      display:flex;align-items:center;justify-content:center;
      flex-shrink:0;
    }

    .sidebar-logo-mark svg{width:18px;height:18px;fill:none;stroke:#38BDF8;stroke-width:2;stroke-linecap:round;}

    .sidebar-brand h1{font-family:var(--font-display);font-size:1rem;font-weight:800;color:#fff;line-height:1;}
    .sidebar-brand p{font-size:0.7rem;color:rgba(255,255,255,0.4);margin-top:2px;}

    .sidebar-nav {
      flex:1;
      padding:1rem 0.75rem;
      display:flex;
      flex-direction:column;
      gap:2px;
    }

    .nav-label {
      font-size:0.65rem;font-weight:700;
      color:rgba(255,255,255,0.3);
      text-transform:uppercase;letter-spacing:0.1em;
      padding:0.75rem 0.75rem 0.25rem;
      margin-top:0.5rem;
    }

    .sidebar-nav a {
      display:flex;align-items:center;gap:10px;
      padding:0.6rem 0.875rem;
      border-radius:8px;
      color:rgba(255,255,255,0.6);
      text-decoration:none;
      font-size:0.9rem;
      font-weight:500;
      transition:all 0.15s;
    }

    .sidebar-nav a svg{width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;flex-shrink:0;}
    .sidebar-nav a:hover{background:rgba(255,255,255,0.08);color:#fff;}
    .sidebar-nav a.active{background:rgba(14,165,233,0.2);color:#38BDF8;}

    .sidebar-footer {
      padding:1rem 0.75rem;
      border-top:1px solid rgba(255,255,255,0.08);
    }

    .user-info {
      display:flex;align-items:center;gap:10px;
      padding:0.75rem;
      border-radius:8px;
      background:rgba(255,255,255,0.05);
      margin-bottom:0.75rem;
    }

    .user-avatar {
      width:32px;height:32px;
      background:rgba(14,165,233,0.3);
      border-radius:50%;
      display:flex;align-items:center;justify-content:center;
      font-size:0.8rem;font-weight:700;color:#38BDF8;
      flex-shrink:0;
    }

    .user-name{font-size:0.85rem;font-weight:600;color:#fff;line-height:1;}
    .user-role{font-size:0.7rem;color:rgba(255,255,255,0.4);margin-top:2px;text-transform:capitalize;}

    .btn-logout {
      display:flex;align-items:center;justify-content:center;gap:8px;
      width:100%;padding:0.6rem;
      background:rgba(239,68,68,0.1);
      color:rgba(239,68,68,0.8);
      border:1px solid rgba(239,68,68,0.2);
      border-radius:8px;
      font-size:0.85rem;font-weight:600;
      text-decoration:none;cursor:pointer;
      transition:all 0.15s;font-family:var(--font-body);
    }

    .btn-logout:hover{background:rgba(239,68,68,0.2);color:#EF4444;}
    .btn-logout svg{width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;}

    /* ─── MAIN ────────────────────────────────────────── */
    .main {
      margin-left:var(--sidebar-w);
      flex:1;
      display:flex;
      flex-direction:column;
      min-height:100vh;
    }

    .topbar {
      background:#fff;
      border-bottom:1px solid var(--slate-200);
      padding:0 2rem;
      height:60px;
      display:flex;align-items:center;justify-content:space-between;
      position:sticky;top:0;z-index:50;
    }

    .topbar h2{font-family:var(--font-display);font-size:1.15rem;font-weight:800;color:var(--navy);}
    .topbar-time{font-size:0.8rem;color:var(--slate-400);}

    .content{padding:2rem;flex:1;}

    /* ─── STATS ───────────────────────────────────────── */
    .stats-grid{
      display:grid;
      grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
      gap:1.25rem;
      margin-bottom:2rem;
    }

    .stat-card{
      background:#fff;
      border:1px solid var(--slate-200);
      border-radius:12px;
      padding:1.5rem;
      position:relative;
      overflow:hidden;
    }

    .stat-card::before{
      content:'';
      position:absolute;top:0;left:0;right:0;
      height:3px;
      background:linear-gradient(90deg,var(--sky),#38BDF8);
    }

    .stat-value{
      font-family:var(--font-display);
      font-size:2.25rem;font-weight:800;
      color:var(--navy);line-height:1;
      margin-bottom:4px;
    }

    .stat-label{font-size:0.8rem;color:var(--slate-400);font-weight:600;text-transform:uppercase;letter-spacing:0.06em;}

    /* ─── TABLE ───────────────────────────────────────── */
    .card{
      background:#fff;
      border:1px solid var(--slate-200);
      border-radius:12px;
      overflow:hidden;
      margin-bottom:1.5rem;
    }

    .card-header{
      padding:1.25rem 1.5rem;
      border-bottom:1px solid var(--slate-200);
      display:flex;align-items:center;justify-content:space-between;
    }

    .card-header h3{font-size:0.95rem;font-weight:700;color:var(--navy);}

    .btn-sm{
      display:inline-flex;align-items:center;gap:6px;
      padding:0.5rem 1rem;
      background:var(--navy);color:#fff;
      border:none;border-radius:8px;
      font-size:0.825rem;font-weight:600;
      text-decoration:none;cursor:pointer;
      font-family:var(--font-body);
      transition:background 0.15s;
    }
    .btn-sm:hover{background:var(--sky-dark);}
    .btn-sm svg{width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;}

    .btn-sm.outline{background:transparent;color:var(--navy);border:1.5px solid var(--slate-200);}
    .btn-sm.outline:hover{background:var(--slate-100);}

    .btn-sm.danger{background:transparent;color:var(--danger);border:1.5px solid rgba(239,68,68,0.3);}
    .btn-sm.danger:hover{background:rgba(239,68,68,0.08);}

    table{width:100%;border-collapse:collapse;}
    thead tr{background:var(--slate-100);}
    th{
      padding:0.75rem 1.5rem;
      text-align:left;
      font-size:0.75rem;font-weight:700;
      color:var(--slate-400);
      text-transform:uppercase;letter-spacing:0.06em;
      white-space:nowrap;
    }
    td{
      padding:1rem 1.5rem;
      font-size:0.9rem;
      border-top:1px solid var(--slate-200);
      color:var(--slate-800);
    }
    tr:hover td{background:var(--slate-100);}

    .badge{
      display:inline-flex;
      padding:3px 10px;
      border-radius:9999px;
      font-size:0.72rem;font-weight:700;
      text-transform:uppercase;letter-spacing:0.06em;
    }
    .badge-published{background:#D1FAE5;color:#065F46;}
    .badge-draft{background:#FEF3C7;color:#92400E;}

    .post-title{font-weight:600;color:var(--navy);}
    .post-date{color:var(--slate-400);font-size:0.82rem;}

    .actions{display:flex;gap:8px;}

    @media(max-width:768px){
      .sidebar{transform:translateX(-100%);transition:transform 0.25s;}
      .sidebar.open{transform:translateX(0);}
      .main{margin-left:0;}
    }
  </style>
</head>
<body>

<!-- ─── SIDEBAR ─────────────────────────────────────────── -->
<aside class="sidebar">
  <div class="sidebar-header">
    <div class="sidebar-logo-mark">
      <svg viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
    </div>
    <div class="sidebar-brand">
      <h1>CoBraLT</h1>
      <p>Painel Admin</p>
    </div>
  </div>

  <nav class="sidebar-nav" aria-label="Menu admin">
    <div class="nav-label">Geral</div>
    <a href="dashboard.php" class="active">
      <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      Dashboard
    </a>

    <div class="nav-label">Conteúdo</div>
    <a href="posts.php">
      <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
      Posts / Notícias
    </a>
    <a href="post-editor.php">
      <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
      Novo Post
    </a>
    <a href="pages.php">
      <svg viewBox="0 0 24 24"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
      Páginas
    </a>

    <div class="nav-label">Sistema</div>
    <a href="usuarios.php">
      <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      Usuários
    </a>
    <a href="../index.php" target="_blank">
      <svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
      Ver site
    </a>
  </nav>

  <div class="sidebar-footer">
    <div class="user-info">
      <div class="user-avatar"><?= strtoupper(substr(e($adminUser), 0, 1)) ?></div>
      <div>
        <div class="user-name"><?= e($adminUser) ?></div>
        <div class="user-role"><?= e($adminRole) ?></div>
      </div>
    </div>
    <a href="../api/logout.php" class="btn-logout">
      <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
      Sair
    </a>
  </div>
</aside>

<!-- ─── MAIN ──────────────────────────────────────────────── -->
<div class="main">
  <div class="topbar">
    <h2>Dashboard</h2>
    <span class="topbar-time"><?= date('d/m/Y H:i') ?></span>
  </div>

  <div class="content">
    <!-- Stats -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-value"><?= (int)$totalPosts ?></div>
        <div class="stat-label">Total de Posts</div>
      </div>
      <div class="stat-card">
        <div class="stat-value"><?= (int)$pubPosts ?></div>
        <div class="stat-label">Posts publicados</div>
      </div>
      <div class="stat-card">
        <div class="stat-value"><?= (int)($totalPosts - $pubPosts) ?></div>
        <div class="stat-label">Rascunhos</div>
      </div>
      <div class="stat-card">
        <div class="stat-value"><?= (int)$totalPages ?></div>
        <div class="stat-label">Páginas</div>
      </div>
    </div>

    <!-- Posts recentes -->
    <div class="card">
      <div class="card-header">
        <h3>Posts recentes</h3>
        <a href="post-editor.php" class="btn-sm">
          <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Novo post
        </a>
      </div>

      <?php if (empty($recentPosts)): ?>
      <div style="padding:3rem;text-align:center;color:var(--slate-400);">
        <p>Nenhum post criado ainda.</p>
        <a href="post-editor.php" class="btn-sm" style="margin-top:1rem;display:inline-flex;">Criar primeiro post</a>
      </div>
      <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>Título</th>
            <th>Status</th>
            <th>Data</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recentPosts as $post): ?>
          <tr>
            <td><span class="post-title"><?= e($post['title']) ?></span></td>
            <td>
              <span class="badge badge-<?= e($post['status']) ?>">
                <?= $post['status'] === 'published' ? 'Publicado' : 'Rascunho' ?>
              </span>
            </td>
            <td><span class="post-date"><?= date('d/m/Y', strtotime($post['created_at'])) ?></span></td>
            <td>
              <div class="actions">
                <a href="post-editor.php?id=<?= (int)$post['id'] ?>" class="btn-sm outline">Editar</a>
                <a href="../api/posts.php?action=delete&id=<?= (int)$post['id'] ?>&csrf=<?= csrf_token() ?>"
                   class="btn-sm danger"
                   onclick="return confirm('Excluir este post?')">Excluir</a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <?php endif; ?>
    </div>
  </div>
</div>

</body>
</html>
