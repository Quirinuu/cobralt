<?php
require_once __DIR__ . '/_auth.php';
require_role('superadmin');

$db = getDB();

$msg   = '';
$error = '';

// ─── CRIAR USUÁRIO ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'create') {
    csrf_verify();

    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role     = in_array($_POST['role'] ?? '', ['superadmin','admin','editor']) ? $_POST['role'] : 'editor';

    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Preencha todos os campos.';
    } elseif (strlen($password) < 10) {
        $error = 'A senha deve ter no mínimo 10 caracteres.';
    } elseif (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password) || !preg_match('/[^a-zA-Z0-9]/', $password)) {
        $error = 'A senha deve conter letras maiúsculas, números e um caractere especial.';
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        try {
            $stmt = $db->prepare('INSERT INTO admin_users (username, email, password_hash, role) VALUES (?,?,?,?)');
            $stmt->execute([$username, $email, $hash, $role]);
            $msg = "Usuário «{$username}» criado com sucesso!";
        } catch (\PDOException $e) {
            $error = str_contains($e->getMessage(), 'Duplicate') ? 'Usuário ou e-mail já cadastrado.' : 'Erro ao criar usuário.';
        }
    }
}

// ─── DESATIVAR / ATIVAR ───────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle') {
    csrf_verify();
    $id = (int)($_POST['id'] ?? 0);
    if ($id !== $adminId) { // não pode desativar a si mesmo
        $db->prepare('UPDATE admin_users SET active = 1 - active WHERE id = ?')->execute([$id]);
    }
}

// ─── EXCLUIR ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    csrf_verify();
    $id = (int)($_POST['id'] ?? 0);
    if ($id !== $adminId) {
        $db->prepare('DELETE FROM admin_users WHERE id = ?')->execute([$id]);
        $msg = 'Usuário excluído.';
    }
}

$users = $db->query('SELECT id, username, email, role, active, last_login, created_at FROM admin_users ORDER BY created_at DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Usuários — Admin CoBraLT</title>
  <meta name="robots" content="noindex, nofollow">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    :root{--navy:#002B4E;--sky:#0EA5E9;--sky-dark:#0284C7;--success:#10B981;--danger:#EF4444;--slate-100:#F1F5F9;--slate-200:#E2E8F0;--slate-400:#94A3B8;--slate-600:#475569;--slate-800:#1E293B;--font-display:'Playfair Display',Georgia,serif;--font-body:'DM Sans','Segoe UI',sans-serif;--sidebar-w:240px;}
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:var(--font-body);background:var(--slate-100);color:var(--slate-800);display:flex;min-height:100vh;}
    .sidebar{width:var(--sidebar-w);background:var(--navy);color:#fff;display:flex;flex-direction:column;position:fixed;top:0;bottom:0;left:0;z-index:100;overflow-y:auto;}
    .sidebar-header{padding:1.5rem 1.25rem 1rem;border-bottom:1px solid rgba(255,255,255,0.08);display:flex;align-items:center;gap:10px;}
    .sidebar-logo-mark{width:36px;height:36px;background:rgba(14,165,233,0.25);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
    .sidebar-logo-mark svg{width:18px;height:18px;fill:none;stroke:#38BDF8;stroke-width:2;stroke-linecap:round;}
    .sidebar-brand h1{font-family:var(--font-display);font-size:1rem;font-weight:800;color:#fff;line-height:1;}
    .sidebar-brand p{font-size:0.7rem;color:rgba(255,255,255,0.4);margin-top:2px;}
    .sidebar-nav{flex:1;padding:1rem 0.75rem;display:flex;flex-direction:column;gap:2px;}
    .nav-label{font-size:0.65rem;font-weight:700;color:rgba(255,255,255,0.3);text-transform:uppercase;letter-spacing:0.1em;padding:0.75rem 0.75rem 0.25rem;margin-top:0.5rem;}
    .sidebar-nav a{display:flex;align-items:center;gap:10px;padding:0.6rem 0.875rem;border-radius:8px;color:rgba(255,255,255,0.6);text-decoration:none;font-size:0.9rem;font-weight:500;transition:all 0.15s;}
    .sidebar-nav a svg{width:16px;height:16px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;flex-shrink:0;}
    .sidebar-nav a:hover{background:rgba(255,255,255,0.08);color:#fff;}
    .sidebar-nav a.active{background:rgba(14,165,233,0.2);color:#38BDF8;}
    .sidebar-footer{padding:1rem 0.75rem;border-top:1px solid rgba(255,255,255,0.08);}
    .user-info{display:flex;align-items:center;gap:10px;padding:0.75rem;border-radius:8px;background:rgba(255,255,255,0.05);margin-bottom:0.75rem;}
    .user-avatar{width:32px;height:32px;background:rgba(14,165,233,0.3);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:700;color:#38BDF8;flex-shrink:0;}
    .user-name{font-size:0.85rem;font-weight:600;color:#fff;line-height:1;}
    .user-role{font-size:0.7rem;color:rgba(255,255,255,0.4);margin-top:2px;text-transform:capitalize;}
    .btn-logout{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:0.6rem;background:rgba(239,68,68,0.1);color:rgba(239,68,68,0.8);border:1px solid rgba(239,68,68,0.2);border-radius:8px;font-size:0.85rem;font-weight:600;text-decoration:none;cursor:pointer;transition:all 0.15s;font-family:var(--font-body);}
    .btn-logout:hover{background:rgba(239,68,68,0.2);color:#EF4444;}
    .btn-logout svg{width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;}
    .main{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column;min-height:100vh;}
    .topbar{background:#fff;border-bottom:1px solid var(--slate-200);padding:0 2rem;height:60px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;}
    .topbar h2{font-family:var(--font-display);font-size:1.15rem;font-weight:800;color:var(--navy);}
    .content{padding:2rem;flex:1;}
    .grid{display:grid;grid-template-columns:1fr 360px;gap:1.5rem;align-items:start;}
    @media(max-width:900px){.grid{grid-template-columns:1fr;}}
    .card{background:#fff;border:1px solid var(--slate-200);border-radius:12px;overflow:hidden;margin-bottom:1.5rem;}
    .card-header{padding:1.25rem 1.5rem;border-bottom:1px solid var(--slate-200);}
    .card-header h3{font-size:0.95rem;font-weight:700;color:var(--navy);}
    .card-body{padding:1.5rem;}
    .form-group{margin-bottom:1.1rem;}
    .form-group:last-child{margin-bottom:0;}
    label{display:block;font-size:0.8rem;font-weight:600;color:var(--slate-600);margin-bottom:5px;text-transform:uppercase;letter-spacing:0.04em;}
    input[type=text],input[type=email],input[type=password],select{width:100%;padding:0.6rem 0.875rem;border:1.5px solid var(--slate-200);border-radius:8px;font-size:0.9rem;font-family:var(--font-body);color:var(--slate-800);background:#fff;transition:border-color 0.15s;}
    input:focus,select:focus{outline:none;border-color:var(--sky);}
    .btn{display:inline-flex;align-items:center;gap:8px;padding:0.65rem 1.25rem;border-radius:8px;font-size:0.9rem;font-weight:600;cursor:pointer;font-family:var(--font-body);transition:all 0.15s;border:none;width:100%;justify-content:center;margin-top:0.5rem;}
    .btn-primary{background:var(--navy);color:#fff;}
    .btn-primary:hover{background:var(--sky-dark);}
    table{width:100%;border-collapse:collapse;}
    thead tr{background:var(--slate-100);}
    th{padding:0.75rem 1.25rem;text-align:left;font-size:0.75rem;font-weight:700;color:var(--slate-400);text-transform:uppercase;letter-spacing:0.06em;}
    td{padding:0.875rem 1.25rem;font-size:0.88rem;border-top:1px solid var(--slate-200);color:var(--slate-800);}
    tr:hover td{background:var(--slate-100);}
    .badge{display:inline-flex;padding:2px 8px;border-radius:9999px;font-size:0.7rem;font-weight:700;text-transform:uppercase;}
    .role-superadmin{background:#EDE9FE;color:#5B21B6;}
    .role-admin{background:#DBEAFE;color:#1E40AF;}
    .role-editor{background:#D1FAE5;color:#065F46;}
    .active-yes{color:var(--success);font-weight:600;}
    .active-no{color:var(--danger);font-weight:600;}
    .actions{display:flex;gap:6px;}
    .btn-xs{display:inline-flex;align-items:center;padding:0.35rem 0.7rem;border-radius:6px;font-size:0.78rem;font-weight:600;cursor:pointer;font-family:var(--font-body);border:none;text-decoration:none;transition:all 0.15s;}
    .btn-xs.outline{background:transparent;color:var(--navy);border:1.5px solid var(--slate-200);}
    .btn-xs.outline:hover{background:var(--slate-100);}
    .btn-xs.danger{background:transparent;color:var(--danger);border:1.5px solid rgba(239,68,68,0.3);}
    .btn-xs.danger:hover{background:rgba(239,68,68,0.08);}
    .btn-xs.warning{background:transparent;color:var(--warning);border:1.5px solid rgba(245,158,11,0.3);}
    .btn-xs.warning:hover{background:rgba(245,158,11,0.08);}
    .you-badge{display:inline-block;font-size:0.68rem;background:#FEF3C7;color:#92400E;padding:1px 6px;border-radius:4px;margin-left:4px;font-weight:700;}
    .alert{padding:0.875rem 1.25rem;border-radius:8px;margin-bottom:1.25rem;font-size:0.875rem;font-weight:500;}
    .alert-success{background:#D1FAE5;color:#065F46;}
    .alert-error{background:#FEE2E2;color:#991B1B;}
    .pwd-hint{font-size:0.75rem;color:var(--slate-400);margin-top:4px;}
  </style>
</head>
<body>

<aside class="sidebar">
  <div class="sidebar-header">
    <div class="sidebar-logo-mark"><svg viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg></div>
    <div class="sidebar-brand"><h1>CoBraLT</h1><p>Painel Admin</p></div>
  </div>
  <nav class="sidebar-nav">
    <div class="nav-label">Geral</div>
    <a href="dashboard.php"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>Dashboard</a>
    <div class="nav-label">Conteúdo</div>
    <a href="posts.php"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/></svg>Posts / Notícias</a>
    <a href="post-editor.php"><svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>Novo Post</a>
    <a href="pages.php"><svg viewBox="0 0 24 24"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/></svg>Páginas</a>
    <div class="nav-label">Sistema</div>
    <a href="usuarios.php" class="active"><svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>Usuários</a>
    <a href="../index.php" target="_blank"><svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>Ver site</a>
  </nav>
  <div class="sidebar-footer">
    <div class="user-info">
      <div class="user-avatar"><?= strtoupper(substr(e($adminUser), 0, 1)) ?></div>
      <div><div class="user-name"><?= e($adminUser) ?></div><div class="user-role"><?= e($adminRole) ?></div></div>
    </div>
    <a href="../api/logout.php" class="btn-logout">
      <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>Sair
    </a>
  </div>
</aside>

<div class="main">
  <div class="topbar"><h2>Gerenciar Usuários</h2></div>
  <div class="content">
    <?php if ($msg): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

    <div class="grid">
      <!-- Lista de usuários -->
      <div class="card">
        <div class="card-header"><h3>Usuários administrativos (<?= count($users) ?>)</h3></div>
        <table>
          <thead><tr><th>Usuário</th><th>Perfil</th><th>Ativo</th><th>Último login</th><th>Ações</th></tr></thead>
          <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
              <td>
                <strong><?= e($u['username']) ?></strong>
                <?php if ($u['id'] === $adminId): ?><span class="you-badge">você</span><?php endif; ?>
                <div style="font-size:0.78rem;color:var(--slate-400);"><?= e($u['email']) ?></div>
              </td>
              <td><span class="badge role-<?= e($u['role']) ?>"><?= e($u['role']) ?></span></td>
              <td>
                <span class="<?= $u['active'] ? 'active-yes' : 'active-no' ?>">
                  <?= $u['active'] ? 'Ativo' : 'Inativo' ?>
                </span>
              </td>
              <td style="color:var(--slate-400);font-size:0.8rem;">
                <?= $u['last_login'] ? date('d/m/Y H:i', strtotime($u['last_login'])) : '—' ?>
              </td>
              <td>
                <?php if ($u['id'] !== $adminId): ?>
                <div class="actions">
                  <form method="POST" style="display:inline">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="action" value="toggle">
                    <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                    <button type="submit" class="btn-xs <?= $u['active'] ? 'warning' : 'outline' ?>">
                      <?= $u['active'] ? 'Desativar' : 'Ativar' ?>
                    </button>
                  </form>
                  <form method="POST" style="display:inline" onsubmit="return confirm('Excluir usuário <?= e($u['username']) ?>?')">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                    <button type="submit" class="btn-xs danger">Excluir</button>
                  </form>
                </div>
                <?php else: ?>
                <span style="font-size:0.78rem;color:var(--slate-400);">conta atual</span>
                <?php endif; ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Criar usuário -->
      <div class="card">
        <div class="card-header"><h3>Adicionar novo usuário</h3></div>
        <div class="card-body">
          <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
              <label for="username">Usuário</label>
              <input type="text" id="username" name="username" placeholder="joaosilva" required>
            </div>
            <div class="form-group">
              <label for="email">E-mail</label>
              <input type="email" id="email" name="email" placeholder="joao@cobralT.org.br" required>
            </div>
            <div class="form-group">
              <label for="password">Senha</label>
              <input type="password" id="password" name="password" placeholder="Mínimo 10 caracteres" required>
              <p class="pwd-hint">Mínimo 10 caracteres • letras maiúsculas • números • caractere especial (!@#$…)</p>
            </div>
            <div class="form-group">
              <label for="role">Perfil de acesso</label>
              <select id="role" name="role">
                <option value="editor">Editor — cria e edita posts</option>
                <option value="admin">Admin — gerencia tudo, exceto usuários</option>
                <option value="superadmin">Superadmin — acesso total</option>
              </select>
            </div>
            <button type="submit" class="btn btn-primary">Criar usuário</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
