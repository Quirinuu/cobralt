<?php
require_once __DIR__ . '/_auth.php';

$db = getDB();

// Estatísticas rápidas
$totalPosts  = $db->query("SELECT COUNT(*) FROM posts WHERE tipo = 'noticias'")->fetchColumn();
$pubPosts    = $db->query("SELECT COUNT(*) FROM posts WHERE tipo = 'noticias' AND status = 'published'")->fetchColumn();
$totalPages  = $db->query('SELECT COUNT(*) FROM pages')->fetchColumn();
$totalSupporters = $db->query('SELECT COUNT(*) FROM apoiadores WHERE ativo = 1')->fetchColumn();
$recentPosts = $db->query("SELECT id, title, slug, status, created_at FROM posts WHERE tipo = 'noticias' ORDER BY created_at DESC LIMIT 8")->fetchAll();
$migrationError = null;
try {
    $migrationStatus = db_migration_status($db);
} catch (Throwable $e) {
    $migrationStatus = [];
    $migrationError = $e->getMessage();
}
$pendingMigrations = count(array_filter(
    $migrationStatus,
    static fn(array $migration): bool => $migration['status'] !== 'applied'
));
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
  <?php admin_shared_css(); ?>
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
    .toast{position:fixed;bottom:2rem;right:2rem;padding:1rem 1.5rem;border-radius:10px;font-weight:600;font-size:0.9rem;z-index:9999;display:none;}
    .toast.success{background:#D1FAE5;color:#065F46;}
    .toast.error{background:#FEE2E2;color:#991B1B;}
    .migration-list{display:flex;flex-direction:column;gap:.65rem;padding:1.25rem 1.5rem;}
    .migration-row{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:.8rem 1rem;background:var(--slate-100);border-radius:8px;}
    .migration-id{font-family:monospace;font-size:.76rem;color:var(--slate-600);}
    .migration-description{font-size:.84rem;color:var(--slate-800);margin-top:.2rem;}
    .migration-badge{display:inline-flex;padding:4px 10px;border-radius:999px;font-size:.7rem;font-weight:700;white-space:nowrap;}
    .migration-badge.applied{background:#D1FAE5;color:#065F46;}
    .migration-badge.pending{background:#FEF3C7;color:#92400E;}
    .migration-badge.changed{background:#FEE2E2;color:#991B1B;}

    @media(max-width:768px){
      .main{margin-left:0;}
    }
  </style>
</head>
<body>

<!-- ─── SIDEBAR ─────────────────────────────────────────── -->
<?php admin_sidebar('dashboard'); ?>

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
        <div class="stat-label">Total de notícias</div>
      </div>
      <div class="stat-card">
        <div class="stat-value"><?= (int)$pubPosts ?></div>
        <div class="stat-label">Notícias publicadas</div>
      </div>
      <div class="stat-card">
        <div class="stat-value"><?= (int)($totalPosts - $pubPosts) ?></div>
        <div class="stat-label">Rascunhos</div>
      </div>
      <div class="stat-card">
        <div class="stat-value"><?= (int)$totalPages ?></div>
        <div class="stat-label">Páginas</div>
      </div>
      <div class="stat-card">
        <div class="stat-value"><?= (int)$totalSupporters ?></div>
        <div class="stat-label">Apoiadores visíveis</div>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h3>Atualizações do banco</h3>
        <span class="migration-badge <?= $pendingMigrations === 0 && !$migrationError ? 'applied' : 'pending' ?>">
          <?= $migrationError ? 'Verificar configuração' : ($pendingMigrations === 0 ? 'Banco atualizado' : $pendingMigrations . ' pendente(s)') ?>
        </span>
      </div>
      <?php if ($migrationError): ?>
        <div class="migration-list">
          <div class="migration-row">
            <div>
              <div class="migration-description"><?= e($migrationError) ?></div>
            </div>
            <span class="migration-badge changed">Erro</span>
          </div>
        </div>
      <?php elseif (empty($migrationStatus)): ?>
        <div class="migration-list">
          <div class="migration-description">Nenhuma migração versionada encontrada.</div>
        </div>
      <?php else: ?>
        <div class="migration-list">
          <?php foreach ($migrationStatus as $migration): ?>
            <div class="migration-row">
              <div>
                <div class="migration-id"><?= e($migration['id']) ?></div>
                <div class="migration-description"><?= e($migration['description']) ?></div>
              </div>
              <span class="migration-badge <?= e($migration['status']) ?>">
                <?= $migration['status'] === 'applied' ? 'Aplicada' : ($migration['status'] === 'changed' ? 'Arquivo alterado' : 'Pendente') ?>
              </span>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Notícias recentes -->
    <div class="card">
      <div class="card-header">
        <h3>Notícias recentes</h3>
        <a href="post-editor.php" class="btn-sm">
          <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
          Nova notícia
        </a>
      </div>

      <?php if (empty($recentPosts)): ?>
      <div style="padding:3rem;text-align:center;color:var(--slate-400);">
        <p>Nenhuma notícia criada ainda.</p>
        <a href="post-editor.php" class="btn-sm" style="margin-top:1rem;display:inline-flex;">Criar primeira notícia</a>
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
                <button type="button" class="btn-sm danger" onclick="deletePost(<?= (int)$post['id'] ?>, this)">Excluir</button>
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

<div class="toast" id="toast"></div>
<script>
const CSRF = '<?= csrf_token() ?>';
function showToast(msg, type = 'success') {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className = 'toast ' + type;
  t.style.display = 'block';
  clearTimeout(t._timer);
  t._timer = setTimeout(() => { t.style.display = 'none'; }, 3500);
}
async function deletePost(id, btn) {
  if (!confirm('Excluir esta notícia?')) return;
  btn.disabled = true;
  const fd = new FormData();
  fd.append('action', 'delete');
  fd.append('id', id);
  fd.append('csrf_token', CSRF);
  try {
    const res = await fetch('../api/posts.php', { method: 'POST', body: fd });
    const data = await res.json();
    if (!data.success) throw new Error(data.message || 'Erro ao excluir.');
    btn.closest('tr').remove();
    showToast('Notícia excluída.');
  } catch (err) {
    showToast(err.message || 'Erro ao excluir.', 'error');
    btn.disabled = false;
  }
}
</script>

</body>
</html>
