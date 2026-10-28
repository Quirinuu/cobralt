<?php
require_once __DIR__ . '/_auth.php';
$db = getDB();

$statusFilter = $_GET['status'] ?? '';
$statusFilter = in_array($statusFilter, ['draft', 'published'], true) ? $statusFilter : '';
$where  = $statusFilter ? 'WHERE p.tipo = ? AND p.status = ?' : 'WHERE p.tipo = ?';
$params = $statusFilter ? ['noticias', $statusFilter] : ['noticias'];

$posts = $db->prepare(
    "SELECT p.id, p.title, p.slug, p.status, p.tipo, p.category, p.published_at, p.created_at,
            COALESCE(u.username, 'Usuario removido') AS author
     FROM posts p
     LEFT JOIN admin_users u ON u.id = p.author_id
     {$where}
     ORDER BY p.created_at DESC"
);
$posts->execute($params);
$posts = $posts->fetchAll();

$total     = $db->query("SELECT COUNT(*) FROM posts WHERE tipo='noticias'")->fetchColumn();
$published = $db->query("SELECT COUNT(*) FROM posts WHERE tipo='noticias' AND status='published'")->fetchColumn();
$drafts    = (int)$total - (int)$published;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Notícias — Admin CoBraLT</title>
  <meta name="robots" content="noindex, nofollow">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <?php admin_shared_css(); ?>
  <style>
    :root{--navy:#002B4E;--sky:#0EA5E9;--sky-dark:#0284C7;--success:#10B981;--danger:#EF4444;--warning:#F59E0B;--slate-100:#F1F5F9;--slate-200:#E2E8F0;--slate-400:#94A3B8;--slate-600:#475569;--slate-800:#1E293B;--font-display:'Playfair Display',Georgia,serif;--font-body:'DM Sans','Segoe UI',sans-serif;--sidebar-w:240px;}
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:var(--font-body);background:var(--slate-100);color:var(--slate-800);display:flex;min-height:100vh;}
    .main{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column;min-height:100vh;}
    .topbar{background:#fff;border-bottom:1px solid var(--slate-200);padding:0 2rem;height:60px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;}
    .topbar h2{font-family:var(--font-display);font-size:1.15rem;font-weight:800;color:var(--navy);}
    .content{padding:2rem;flex:1;}
    .filters{display:flex;gap:0.5rem;margin-bottom:1.5rem;flex-wrap:wrap;align-items:center;}
    .filter-btn{padding:0.4rem 1rem;border-radius:9999px;font-size:0.82rem;font-weight:600;text-decoration:none;border:1.5px solid var(--slate-200);color:var(--slate-600);transition:all 0.15s;}
    .filter-btn:hover,.filter-btn.active{background:var(--navy);border-color:var(--navy);color:#fff;}
    .filter-count{display:inline-flex;align-items:center;justify-content:center;min-width:20px;height:20px;padding:0 5px;border-radius:9999px;font-size:0.7rem;font-weight:700;background:rgba(0,0,0,0.12);margin-left:5px;}
    .card{background:#fff;border:1px solid var(--slate-200);border-radius:12px;overflow:hidden;margin-bottom:1.5rem;}
    .card-header{padding:1.25rem 1.5rem;border-bottom:1px solid var(--slate-200);display:flex;align-items:center;justify-content:space-between;}
    .card-header h3{font-size:0.95rem;font-weight:700;color:var(--navy);}
    .btn-sm{display:inline-flex;align-items:center;gap:6px;padding:0.5rem 1rem;background:var(--navy);color:#fff;border:none;border-radius:8px;font-size:0.825rem;font-weight:600;text-decoration:none;cursor:pointer;font-family:var(--font-body);transition:background 0.15s;}
    .btn-sm:hover{background:var(--sky-dark);}
    .btn-sm svg{width:14px;height:14px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;}
    .btn-sm.outline{background:transparent;color:var(--navy);border:1.5px solid var(--slate-200);}
    .btn-sm.outline:hover{background:var(--slate-100);}
    .btn-sm.danger{background:transparent;color:var(--danger);border:1.5px solid rgba(239,68,68,0.3);}
    .btn-sm.danger:hover{background:rgba(239,68,68,0.08);}
    table{width:100%;border-collapse:collapse;}
    thead tr{background:var(--slate-100);}
    th{padding:0.75rem 1.5rem;text-align:left;font-size:0.75rem;font-weight:700;color:var(--slate-400);text-transform:uppercase;letter-spacing:0.06em;white-space:nowrap;}
    td{padding:0.875rem 1.5rem;font-size:0.9rem;border-top:1px solid var(--slate-200);color:var(--slate-800);}
    tr:hover td{background:var(--slate-100);}
    .badge{display:inline-flex;padding:3px 10px;border-radius:9999px;font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;}
    .badge-published{background:#D1FAE5;color:#065F46;}
    .badge-draft{background:#FEF3C7;color:#92400E;}
    .post-title{font-weight:600;color:var(--navy);}
    .post-date,.post-author{color:var(--slate-400);font-size:0.82rem;}
    .actions{display:flex;gap:8px;}
    .empty-state{padding:3rem;text-align:center;color:var(--slate-400);}
    .empty-state p{margin-bottom:1rem;}
    .category-tag{display:inline-block;padding:2px 8px;background:rgba(14,165,233,0.1);color:var(--sky-dark);border-radius:4px;font-size:0.75rem;font-weight:600;}
    .toast{position:fixed;bottom:2rem;right:2rem;padding:1rem 1.5rem;border-radius:10px;font-weight:600;font-size:0.9rem;z-index:9999;display:none;animation:slideIn 0.25s ease;}
    .toast.success{background:#D1FAE5;color:#065F46;}
    .toast.error{background:#FEE2E2;color:#991B1B;}
    @keyframes slideIn{from{transform:translateY(20px);opacity:0}to{transform:translateY(0);opacity:1}}
  </style>
</head>
<body>

<?php admin_sidebar('posts'); ?>

<div class="main">
  <div class="topbar">
    <h2>Notícias</h2>
    <a href="post-editor.php" class="btn-sm">
      <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Nova notícia
    </a>
  </div>

  <div class="content">
    <div class="filters">
      <a href="posts.php" class="filter-btn <?= !$statusFilter ? 'active' : '' ?>">
        Todos <span class="filter-count"><?= (int)$total ?></span>
      </a>
      <a href="posts.php?status=published" class="filter-btn <?= $statusFilter === 'published' ? 'active' : '' ?>">
        Publicados <span class="filter-count"><?= (int)$published ?></span>
      </a>
      <a href="posts.php?status=draft" class="filter-btn <?= $statusFilter === 'draft' ? 'active' : '' ?>">
        Rascunhos <span class="filter-count"><?= (int)$drafts ?></span>
      </a>
    </div>

    <div class="card">
      <div class="card-header">
        <h3>
          <?php
            if ($statusFilter === 'published') echo 'Notícias publicadas';
            elseif ($statusFilter === 'draft') echo 'Rascunhos';
            else echo 'Todas as notícias';
          ?>
        </h3>
      </div>

      <?php if (empty($posts)): ?>
      <div class="empty-state">
        <p>Nenhuma notícia encontrada.</p>
        <a href="post-editor.php" class="btn-sm">Criar primeira notícia</a>
      </div>
      <?php else: ?>
      <table>
        <thead>
          <tr>
            <th>Título</th>
            <th>Categoria</th>
            <th>Status</th>
            <th>Autor</th>
            <th>Data</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($posts as $post): ?>
          <tr>
            <td><span class="post-title"><?= e($post['title']) ?></span></td>
            <td>
              <?php if ($post['category']): ?>
              <span class="category-tag"><?= e($post['category']) ?></span>
              <?php else: ?>
              <span class="post-date">—</span>
              <?php endif; ?>
            </td>
            <td>
              <span class="badge badge-<?= e($post['status']) ?>">
                <?= $post['status'] === 'published' ? 'Publicado' : 'Rascunho' ?>
              </span>
            </td>
            <td><span class="post-author"><?= e($post['author']) ?></span></td>
            <td><span class="post-date"><?= date('d/m/Y', strtotime($post['created_at'])) ?></span></td>
            <td>
              <div class="actions">
                <a href="post-editor.php?id=<?= (int)$post['id'] ?>" class="btn-sm outline">Editar</a>
                <button class="btn-sm danger" onclick="deletePost(<?= (int)$post['id'] ?>, this)">Excluir</button>
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
  setTimeout(() => { t.style.display = 'none'; }, 3500);
}

async function deletePost(id, btn) {
  if (!confirm('Excluir esta notícia permanentemente?')) return;
  btn.disabled = true;
  const fd = new FormData();
  fd.append('action', 'delete');
  fd.append('id', id);
  fd.append('csrf_token', CSRF);
  const res = await fetch('../api/posts.php', { method: 'POST', body: fd });
  const data = await res.json();
  if (data.success) {
    btn.closest('tr').remove();
    showToast('Post excluído com sucesso.');
  } else {
    showToast(data.message || 'Erro ao excluir.', 'error');
    btn.disabled = false;
  }
}
</script>
</body>
</html>
