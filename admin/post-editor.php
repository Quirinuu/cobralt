<?php
require_once __DIR__ . '/_auth.php';
$db = getDB();

$postId = (int)($_GET['id'] ?? 0);
$post   = null;

if ($postId) {
    $stmt = $db->prepare('SELECT * FROM posts WHERE id = ?');
    $stmt->execute([$postId]);
    $post = $stmt->fetch();
    if (!$post) {
        header('Location: posts.php');
        exit;
    }
}

$pageTitle = $post ? 'Editar Post' : 'Novo Post';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?> — Admin CoBraLT</title>
  <meta name="robots" content="noindex, nofollow">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
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
    .topbar-actions{display:flex;gap:0.75rem;}
    .content{padding:2rem;flex:1;}
    .editor-grid{display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start;}
    @media(max-width:900px){.editor-grid{grid-template-columns:1fr;}}
    .card{background:#fff;border:1px solid var(--slate-200);border-radius:12px;overflow:hidden;margin-bottom:1.5rem;}
    .card-header{padding:1.25rem 1.5rem;border-bottom:1px solid var(--slate-200);}
    .card-header h3{font-size:0.95rem;font-weight:700;color:var(--navy);}
    .card-body{padding:1.5rem;}
    .form-group{margin-bottom:1.25rem;}
    .form-group:last-child{margin-bottom:0;}
    label{display:block;font-size:0.8rem;font-weight:600;color:var(--slate-600);margin-bottom:6px;text-transform:uppercase;letter-spacing:0.04em;}
    input[type=text],input[type=url],select,textarea{width:100%;padding:0.6rem 0.875rem;border:1.5px solid var(--slate-200);border-radius:8px;font-size:0.9rem;font-family:var(--font-body);color:var(--slate-800);background:#fff;transition:border-color 0.15s;}
    input[type=text]:focus,input[type=url]:focus,select:focus,textarea:focus{outline:none;border-color:var(--sky);}
    textarea{resize:vertical;min-height:80px;}
    .quill-wrap{border:1.5px solid var(--slate-200);border-radius:8px;overflow:hidden;}
    .quill-wrap .ql-toolbar{border:none;border-bottom:1.5px solid var(--slate-200);background:var(--slate-100);}
    .quill-wrap .ql-container{border:none;font-family:var(--font-body);font-size:1rem;}
    .quill-wrap .ql-editor{min-height:350px;padding:1rem;}
    .btn{display:inline-flex;align-items:center;gap:8px;padding:0.65rem 1.5rem;border-radius:8px;font-size:0.9rem;font-weight:600;cursor:pointer;font-family:var(--font-body);transition:all 0.15s;border:none;}
    .btn-primary{background:var(--navy);color:#fff;}
    .btn-primary:hover{background:var(--sky-dark);}
    .btn-success{background:var(--success);color:#fff;}
    .btn-success:hover{filter:brightness(0.9);}
    .btn-outline{background:transparent;color:var(--slate-600);border:1.5px solid var(--slate-200);}
    .btn-outline:hover{background:var(--slate-100);}
    .btn svg{width:15px;height:15px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;}
    .status-toggle{display:flex;gap:0.5rem;}
    .status-toggle label{text-transform:none;font-size:0.9rem;font-weight:500;cursor:pointer;padding:0.5rem 1rem;border:1.5px solid var(--slate-200);border-radius:8px;transition:all 0.15s;margin:0;}
    .status-toggle input{position:absolute;opacity:0;pointer-events:none;}
    .status-toggle input:checked + label{border-color:var(--sky);background:rgba(14,165,233,0.08);color:var(--sky-dark);}
    .cover-preview{margin-top:0.75rem;border-radius:8px;overflow:hidden;display:none;}
    .cover-preview img{width:100%;height:160px;object-fit:cover;}
    .upload-area{border:2px dashed var(--slate-200);border-radius:8px;padding:1.5rem;text-align:center;cursor:pointer;transition:all 0.15s;font-size:0.85rem;color:var(--slate-400);}
    .upload-area:hover{border-color:var(--sky);color:var(--sky);}
    .upload-area svg{width:24px;height:24px;fill:none;stroke:currentColor;stroke-width:1.5;margin:0 auto 0.5rem;display:block;}
    #fileInput{display:none;}
    .toast{position:fixed;bottom:2rem;right:2rem;padding:1rem 1.5rem;border-radius:10px;font-weight:600;font-size:0.9rem;z-index:9999;display:none;}
    .toast.success{background:#D1FAE5;color:#065F46;}
    .toast.error{background:#FEE2E2;color:#991B1B;}
    .saving-indicator{font-size:0.8rem;color:var(--slate-400);}
  </style>
</head>
<body>

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
  <nav class="sidebar-nav">
    <div class="nav-label">Geral</div>
    <a href="dashboard.php">
      <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      Dashboard
    </a>
    <div class="nav-label">Conteúdo</div>
    <a href="posts.php">
      <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
      Posts / Notícias
    </a>
    <a href="post-editor.php" class="active">
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

<div class="main">
  <div class="topbar">
    <h2><?= e($pageTitle) ?></h2>
    <div class="topbar-actions">
      <span class="saving-indicator" id="savingIndicator"></span>
      <a href="posts.php" class="btn btn-outline">← Voltar</a>
      <button class="btn btn-outline" onclick="savePost('draft')">
        <svg viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
        Salvar rascunho
      </button>
      <button class="btn btn-success" onclick="savePost('published')">
        <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        Publicar
      </button>
    </div>
  </div>

  <div class="content">
    <div class="editor-grid">
      <!-- Coluna principal -->
      <div>
        <div class="card">
          <div class="card-body">
            <div class="form-group">
              <label for="title">Título *</label>
              <input type="text" id="title" placeholder="Ex: Congresso Nacional de Trauma 2025"
                     value="<?= e($post['title'] ?? '') ?>">
            </div>
            <div class="form-group">
              <label>Conteúdo *</label>
              <div class="quill-wrap">
                <div id="quill-editor"><?= $post['content'] ?? '' ?></div>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header"><h3>Resumo / Excerpt</h3></div>
          <div class="card-body">
            <div class="form-group">
              <label for="excerpt">Aparece na listagem de notícias</label>
              <textarea id="excerpt" rows="3" placeholder="Breve descrição do post..."><?= e($post['excerpt'] ?? '') ?></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- Coluna lateral -->
      <div>
        <div class="card">
          <div class="card-header"><h3>Publicação</h3></div>
          <div class="card-body">
            <div class="form-group">
              <label>Status atual</label>
              <div class="status-toggle">
                <input type="radio" name="statusRadio" id="statusDraft" value="draft"
                  <?= (!$post || $post['status'] === 'draft') ? 'checked' : '' ?>>
                <label for="statusDraft">Rascunho</label>
                <input type="radio" name="statusRadio" id="statusPublished" value="published"
                  <?= ($post && $post['status'] === 'published') ? 'checked' : '' ?>>
                <label for="statusPublished">Publicado</label>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header"><h3>Categoria</h3></div>
          <div class="card-body">
            <div class="form-group">
              <label for="category">Categoria</label>
              <select id="category">
                <option value="">— Sem categoria —</option>
                <?php
                $cats = ['Notícia','Projeto','Evento','Publicação','Institucional'];
                foreach ($cats as $c):
                  $sel = ($post['category'] ?? '') === $c ? 'selected' : '';
                ?>
                <option value="<?= e($c) ?>" <?= $sel ?>><?= e($c) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header"><h3>Imagem de Capa</h3></div>
          <div class="card-body">
            <div id="coverPreview" class="cover-preview" style="<?= !empty($post['cover_image']) ? 'display:block' : '' ?>">
              <img id="coverImg" src="<?= e($post['cover_image'] ?? '') ?>" alt="Capa">
            </div>
            <div class="upload-area" onclick="document.getElementById('fileInput').click()">
              <svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
              Clique para selecionar imagem<br>
              <small>JPG, PNG, WebP — máx 5MB</small>
            </div>
            <input type="file" id="fileInput" accept="image/*">
            <input type="hidden" id="coverUrl" value="<?= e($post['cover_image'] ?? '') ?>">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script src="https://cdn.quilljs.com/1.3.7/quill.js"></script>
<script>
const POST_ID  = <?= $postId ?: 'null' ?>;
const CSRF     = '<?= csrf_token() ?>';

// Editor Quill
const quill = new Quill('#quill-editor', {
  theme: 'snow',
  modules: {
    toolbar: [
      [{ header: [2, 3, 4, false] }],
      ['bold', 'italic', 'underline', 'strike'],
      ['link', 'blockquote'],
      [{ list: 'ordered' }, { list: 'bullet' }],
      ['clean']
    ]
  }
});

// Upload de imagem de capa
document.getElementById('fileInput').addEventListener('change', async (e) => {
  const file = e.target.files[0];
  if (!file) return;

  const fd = new FormData();
  fd.append('image', file);
  fd.append('csrf_token', CSRF);

  showToast('Enviando imagem...', 'info');
  const res  = await fetch('../api/upload.php', { method: 'POST', body: fd });
  const data = await res.json();

  if (data.success) {
    document.getElementById('coverUrl').value = data.url;
    document.getElementById('coverImg').src   = data.url;
    document.getElementById('coverPreview').style.display = 'block';
    showToast('Imagem carregada!');
  } else {
    showToast(data.message || 'Erro no upload.', 'error');
  }
});

// Salvar
async function savePost(forcedStatus) {
  const title   = document.getElementById('title').value.trim();
  const content = quill.root.innerHTML;
  const excerpt = document.getElementById('excerpt').value.trim();
  const cat     = document.getElementById('category').value;
  const cover   = document.getElementById('coverUrl').value;
  const status  = forcedStatus ||
    document.querySelector('input[name=statusRadio]:checked')?.value || 'draft';

  if (!title) { showToast('O título é obrigatório.', 'error'); return; }
  if (!quill.getText().trim()) { showToast('O conteúdo não pode ficar vazio.', 'error'); return; }

  document.getElementById('savingIndicator').textContent = 'Salvando…';

  const fd = new FormData();
  fd.append('action',      POST_ID ? 'update' : 'create');
  fd.append('csrf_token',  CSRF);
  fd.append('title',       title);
  fd.append('content',     content);
  fd.append('excerpt',     excerpt);
  fd.append('category',    cat);
  fd.append('status',      status);
  fd.append('cover_image', cover);
  if (POST_ID) fd.append('id', POST_ID);

  const res  = await fetch('../api/posts.php', { method: 'POST', body: fd });
  const data = await res.json();

  document.getElementById('savingIndicator').textContent = '';

  if (data.success) {
    showToast(status === 'published' ? 'Post publicado!' : 'Rascunho salvo!');
    if (!POST_ID && data.id) {
      setTimeout(() => {
        window.location.href = 'post-editor.php?id=' + data.id;
      }, 1200);
    }
    // Atualiza rádio
    document.getElementById(status === 'published' ? 'statusPublished' : 'statusDraft').checked = true;
  } else {
    showToast(data.message || 'Erro ao salvar.', 'error');
  }
}

function showToast(msg, type = 'success') {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className   = 'toast ' + type;
  t.style.display = 'block';
  clearTimeout(t._timer);
  t._timer = setTimeout(() => { t.style.display = 'none'; }, 3500);
}
</script>
</body>
</html>
