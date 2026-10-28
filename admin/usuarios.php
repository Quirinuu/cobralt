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
    } elseif (!preg_match('/^[a-zA-Z0-9._-]{3,60}$/', $username)) {
        $error = 'Use um usuario com 3 a 60 caracteres, apenas letras, numeros, ponto, hifen ou underline.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Informe um e-mail valido.';
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
    if ($id <= 0) {
        $id = $adminId;
    }
    if ($id !== $adminId) { // não pode desativar a si mesmo
        $db->prepare('UPDATE admin_users SET active = 1 - active WHERE id = ?')->execute([$id]);
    }
}

// ─── EXCLUIR ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    csrf_verify();
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0 && $id !== $adminId) {
        try {
            $db->prepare('DELETE FROM admin_users WHERE id = ?')->execute([$id]);
            $msg = 'Usuario excluido.';
        } catch (\PDOException $e) {
            $error = 'Nao foi possivel excluir este usuario. Desative-o para preservar o historico de conteudo.';
        }
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
  <?php admin_shared_css(); ?>
  <style>
    :root{--navy:#002B4E;--sky:#0EA5E9;--sky-dark:#0284C7;--success:#10B981;--danger:#EF4444;--slate-100:#F1F5F9;--slate-200:#E2E8F0;--slate-400:#94A3B8;--slate-600:#475569;--slate-800:#1E293B;--font-display:'Playfair Display',Georgia,serif;--font-body:'DM Sans','Segoe UI',sans-serif;--sidebar-w:240px;}
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:var(--font-body);background:var(--slate-100);color:var(--slate-800);display:flex;min-height:100vh;}
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

<?php admin_sidebar('usuarios'); ?>

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
              <input type="email" id="email" name="email" placeholder="joao@cobralt.com.br" required>
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
