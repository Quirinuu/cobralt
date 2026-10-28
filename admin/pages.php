<?php
require_once __DIR__ . '/_auth.php';
require_role('superadmin', 'admin');

$db    = getDB();
$pages = $db->query(
    "SELECT p.id, p.title, p.slug, p.status, p.created_at,
            COALESCE(u.username, 'Usuario removido') AS author
     FROM pages p LEFT JOIN admin_users u ON u.id = p.author_id
     ORDER BY p.created_at DESC"
)->fetchAll();

// Páginas originais do site: cada arquivo em pages/ + a home. Uma página criada aqui com o mesmo slug substitui a original.
$bySlug = [];
foreach ($pages as $p) { $bySlug[$p['slug']] = $p; }
$siteSlugs = ['home'];
foreach (glob(dirname(__DIR__) . '/pages/*.php') ?: [] as $f) {
    $slug = basename($f, '.php');
    if (!in_array($slug, ['page', 'post', 'login'], true)) { $siteSlugs[] = $slug; }
}
sort($siteSlugs, SORT_NATURAL);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Páginas — Admin CoBraLT</title>
  <meta name="robots" content="noindex, nofollow">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <?php admin_shared_css(); ?>
  <style>
    :root{--navy:#002B4E;--sky:#0EA5E9;--sky-dark:#0284C7;--danger:#EF4444;--slate-100:#F1F5F9;--slate-200:#E2E8F0;--slate-400:#94A3B8;--slate-600:#475569;--slate-800:#1E293B;--font-display:'Playfair Display',Georgia,serif;--font-body:'DM Sans','Segoe UI',sans-serif;--sidebar-w:240px;}
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:var(--font-body);background:var(--slate-100);color:var(--slate-800);display:flex;min-height:100vh;}
    .main{margin-left:var(--sidebar-w);flex:1;display:flex;flex-direction:column;min-height:100vh;}
    .topbar{background:#fff;border-bottom:1px solid var(--slate-200);padding:0 2rem;height:60px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;}
    .topbar h2{font-family:var(--font-display);font-size:1.15rem;font-weight:800;color:var(--navy);}
    .content{padding:2rem;flex:1;}
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
    th{padding:0.75rem 1.5rem;text-align:left;font-size:0.75rem;font-weight:700;color:var(--slate-400);text-transform:uppercase;letter-spacing:0.06em;}
    td{padding:0.875rem 1.5rem;font-size:0.9rem;border-top:1px solid var(--slate-200);color:var(--slate-800);}
    tr:hover td{background:var(--slate-100);}
    .badge{display:inline-flex;padding:3px 10px;border-radius:9999px;font-size:0.72rem;font-weight:700;text-transform:uppercase;letter-spacing:0.06em;}
    .badge-published{background:#D1FAE5;color:#065F46;}
    .badge-draft{background:#FEF3C7;color:#92400E;}
    .page-title{font-weight:600;color:var(--navy);}
    .page-slug{font-size:0.8rem;color:var(--slate-400);font-family:monospace;}
    .actions{display:flex;gap:8px;}
    .empty-state{padding:3rem;text-align:center;color:var(--slate-400);}
    .empty-state p{margin-bottom:1rem;}
    .toast{position:fixed;bottom:2rem;right:2rem;padding:1rem 1.5rem;border-radius:10px;font-weight:600;font-size:0.9rem;z-index:9999;display:none;}
    .toast.success{background:#D1FAE5;color:#065F46;}
    .toast.error{background:#FEE2E2;color:#991B1B;}
    .info-box{background:#EFF6FF;border:1px solid #BFDBFE;border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;font-size:0.875rem;color:#1E40AF;}
  </style>
</head>
<body>

<?php admin_sidebar('pages'); ?>

<div class="main">
  <div class="topbar">
    <h2>Páginas</h2>
    <a href="page-editor.php" class="btn-sm">
      <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Nova página
    </a>
  </div>
  <div class="content">
    <div class="info-box">
      Páginas criadas aqui usam blocos editáveis e ficam disponíveis em <code>/pagina/seu-slug</code>. O editor permite ligar ou desligar títulos, textos, imagens, cores, botões, cards, galerias e seções.
    </div>

    <div class="card">
      <div class="card-header"><h3>Páginas do site (<?= count($siteSlugs) ?>)</h3></div>
      <div class="info-box" style="margin:1rem;">
        Clique em <b>Personalizar</b> para criar uma versão editável de qualquer página do site. Enquanto estiver como rascunho (ou sem versão), a página original continua no ar; ao publicar, a sua versão a substitui. Excluir a versão personalizada restaura a original.
      </div>
      <table>
        <thead><tr><th>Página</th><th>Situação</th><th>Ações</th></tr></thead>
        <tbody>
          <?php foreach ($siteSlugs as $slug): $custom = $bySlug[$slug] ?? null; $url = $slug === 'home' ? '../index.php' : '../pages/' . $slug . '.php'; ?>
          <tr>
            <td><a class="page-slug" href="<?= e($url) ?>" target="_blank"><?= e($slug) ?></a></td>
            <td><?= !$custom ? 'Original' : ($custom['status'] === 'published' ? 'Personalizada (publicada)' : 'Rascunho (original em uso)') ?></td>
            <td><div class="actions">
              <a href="page-editor.php?<?= $custom ? 'id=' . (int)$custom['id'] : 'slug=' . e($slug) ?>" class="btn-sm outline"><?= $custom ? 'Editar' : 'Personalizar' ?></a>
            </div></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="card">
      <div class="card-header"><h3>Todas as páginas (<?= count($pages) ?>)</h3></div>
      <?php if (empty($pages)): ?>
      <div class="empty-state">
        <p>Nenhuma página criada ainda.</p>
        <a href="page-editor.php" class="btn-sm">Criar primeira página</a>
      </div>
      <?php else: ?>
      <table>
        <thead>
          <tr><th>Título</th><th>Slug / URL</th><th>Status</th><th>Autor</th><th>Data</th><th>Ações</th></tr>
        </thead>
        <tbody>
          <?php foreach ($pages as $page): ?>
          <tr>
            <td><span class="page-title"><?= e($page['title']) ?></span></td>
            <td><a class="page-slug" href="../pages/page.php?slug=<?= e($page['slug']) ?>" target="_blank">/pagina/<?= e($page['slug']) ?></a></td>
            <td>
              <span class="badge badge-<?= e($page['status']) ?>">
                <?= $page['status'] === 'published' ? 'Publicada' : 'Rascunho' ?>
              </span>
            </td>
            <td style="color:var(--slate-400);font-size:0.82rem;"><?= e($page['author']) ?></td>
            <td style="color:var(--slate-400);font-size:0.82rem;"><?= date('d/m/Y', strtotime($page['created_at'])) ?></td>
            <td>
              <div class="actions">
                <a href="page-editor.php?id=<?= (int)$page['id'] ?>" class="btn-sm outline">Editar</a>
                <a href="../pages/page.php?slug=<?= e($page['slug']) ?>" target="_blank" class="btn-sm outline">Ver</a>
                <button class="btn-sm danger" onclick="deletePage(<?= (int)$page['id'] ?>, this)">Excluir</button>
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
function showToast(msg, type='success'){const t=document.getElementById('toast');t.textContent=msg;t.className='toast '+type;t.style.display='block';setTimeout(()=>{t.style.display='none'},3500);}
async function deletePage(id, btn){
  if(!confirm('Excluir esta página permanentemente?'))return;
  btn.disabled=true;
  const fd=new FormData();fd.append('action','delete');fd.append('id',id);fd.append('csrf_token',CSRF);
  const res=await fetch('../api/pages.php',{method:'POST',body:fd});
  const data=await res.json();
  if(data.success){btn.closest('tr').remove();showToast('Página excluída.');}
  else{showToast(data.message||'Erro ao excluir.','error');btn.disabled=false;}
}
</script>
</body>
</html>
