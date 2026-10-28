<?php
require_once __DIR__ . '/_auth.php';
require_once dirname(__DIR__) . '/includes/page_builder.php';
require_role('superadmin', 'admin');

$db     = getDB();
$pageId = (int)($_GET['id'] ?? 0);
$page   = null;

if ($pageId) {
    $stmt = $db->prepare('SELECT * FROM pages WHERE id = ?');
    $stmt->execute([$pageId]);
    $page = $stmt->fetch();
    if (!$page) { header('Location: pages.php'); exit; }
}

// ?slug=x: abre a versão personalizada de uma página do site (ou prepara uma nova com esse slug).
$presetSlug = '';
if (!$page && isset($_GET['slug'])) {
    $presetSlug = preg_replace('/[^a-z0-9\-]/i', '', (string)$_GET['slug']) ?? '';
    $st = $db->prepare('SELECT * FROM pages WHERE slug = ?');
    $st->execute([$presetSlug]);
    $page = $st->fetch() ?: null;
    if ($page) { header('Location: page-editor.php?id=' . (int)$page['id']); exit; }
}

$pageTitle = $page ? 'Editar Pagina' : 'Nova Pagina';
$builder   = pb_decode_content($page['content'] ?? '', $page['title'] ?? ucfirst(str_replace('-', ' ', $presetSlug)));
$blocksJson = json_encode(
    $builder['blocks'],
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?> — Admin CoBraLT</title>
  <meta name="robots" content="noindex, nofollow">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <?php admin_shared_css(); ?>
  <style>
    :root{--navy:#002B4E;--sky:#0EA5E9;--sky-dark:#0284C7;--success:#10B981;--danger:#EF4444;--slate-50:#F8FAFC;--slate-100:#F1F5F9;--slate-200:#E2E8F0;--slate-300:#CBD5E1;--slate-400:#94A3B8;--slate-500:#64748B;--slate-600:#475569;--slate-800:#1E293B;--font-display:'Playfair Display',Georgia,serif;--font-body:'DM Sans','Segoe UI',sans-serif;--sidebar-w:240px;}
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:var(--font-body);background:var(--slate-100);color:var(--slate-800);display:flex;min-height:100vh;}
    .sidebar-logo-mark svg,.sidebar-nav a svg,.btn svg,.btn-logout svg{fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;}
    .main{margin-left:var(--sidebar-w);flex:1;min-height:100vh;}
    .topbar{height:64px;background:#fff;border-bottom:1px solid var(--slate-200);padding:0 1.5rem;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50;}
    .topbar h2{font-family:var(--font-display);font-size:1.18rem;font-weight:800;color:var(--navy);}
    .topbar-actions{display:flex;gap:0.65rem;align-items:center;flex-wrap:wrap;}
    .saving-indicator{font-size:0.8rem;color:var(--slate-400);min-width:90px;text-align:right;}
    .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;padding:0.65rem 1rem;border-radius:8px;font-size:0.86rem;font-weight:800;cursor:pointer;font-family:var(--font-body);transition:all 0.15s;border:none;text-decoration:none;white-space:nowrap;}
    .btn svg{width:15px;height:15px;}
    .btn-primary{background:var(--navy);color:#fff;}
    .btn-primary:hover{background:var(--sky-dark);}
    .btn-success{background:var(--success);color:#fff;}
    .btn-outline{background:transparent;color:var(--slate-600);border:1.5px solid var(--slate-200);}
    .btn-outline:hover{background:var(--slate-100);}
    .content{padding:1.5rem;}
    .page-settings{display:grid;grid-template-columns:minmax(240px,1fr) auto;gap:1rem;align-items:end;margin-bottom:1rem;}
    .panel{background:#fff;border:1px solid var(--slate-200);border-radius:12px;box-shadow:0 10px 30px rgba(15,23,42,0.04);}
    .settings-panel{padding:1.25rem;}
    label.field{display:flex;flex-direction:column;gap:6px;font-size:0.78rem;font-weight:800;color:var(--slate-600);text-transform:uppercase;letter-spacing:0.04em;}
    input[type=text],input[type=color],select,textarea{width:100%;border:1.5px solid var(--slate-200);border-radius:8px;background:#fff;color:var(--slate-800);font-family:var(--font-body);font-size:0.9rem;padding:0.65rem 0.8rem;transition:border-color 0.15s,box-shadow 0.15s;}
    textarea{resize:vertical;line-height:1.55;text-transform:none;font-weight:500;letter-spacing:0;}
    input:focus,select:focus,textarea:focus{outline:none;border-color:var(--sky);box-shadow:0 0 0 3px rgba(14,165,233,0.1);}
    .status-toggle{display:flex;gap:0.5rem;align-items:center;justify-content:flex-end;}
    .status-toggle label{font-size:0.85rem;font-weight:800;cursor:pointer;padding:0.62rem 0.95rem;border:1.5px solid var(--slate-200);border-radius:8px;transition:all 0.15s;margin:0;background:#fff;color:var(--slate-600);}
    .status-toggle input{position:absolute;opacity:0;pointer-events:none;}
    .status-toggle input:checked + label{border-color:var(--sky);background:rgba(14,165,233,0.08);color:var(--sky-dark);}
    .builder-shell{display:grid;grid-template-columns:330px minmax(0,1fr);gap:1rem;align-items:start;}
    .blocks-panel{position:sticky;top:82px;overflow:hidden;}
    .panel-head{padding:1rem;border-bottom:1px solid var(--slate-200);display:flex;align-items:center;justify-content:space-between;gap:0.75rem;}
    .panel-head h3{font-size:0.9rem;color:var(--navy);}
    .block-types{padding:0.8rem;display:grid;grid-template-columns:1fr 1fr;gap:0.45rem;border-bottom:1px solid var(--slate-200);background:var(--slate-50);}
    .block-types button,.btn-mini{border:1px solid var(--slate-200);background:#fff;color:var(--slate-700);border-radius:8px;padding:0.55rem 0.6rem;font-family:var(--font-body);font-size:0.78rem;font-weight:800;cursor:pointer;}
    .block-types button:hover,.btn-mini:hover{border-color:var(--sky);color:var(--sky-dark);}
    .blocks-list{padding:0.75rem;display:flex;flex-direction:column;gap:0.55rem;max-height:calc(100vh - 300px);overflow:auto;}
    .builder-block{display:grid;grid-template-columns:1fr auto;gap:0.45rem;align-items:stretch;border:1.5px solid var(--slate-200);border-radius:10px;background:#fff;padding:0.35rem;}
    .builder-block.active{border-color:var(--sky);box-shadow:0 0 0 3px rgba(14,165,233,0.1);}
    .builder-block-main{text-align:left;background:transparent;border:none;cursor:pointer;padding:0.45rem;font-family:var(--font-body);display:flex;flex-direction:column;gap:2px;}
    .builder-block-main strong{font-size:0.86rem;color:var(--navy);line-height:1.25;}
    .builder-block-main small{font-size:0.72rem;color:var(--slate-400);}
    .builder-block-type{font-size:0.65rem;font-weight:900;color:var(--sky-dark);text-transform:uppercase;letter-spacing:0.06em;}
    .builder-block-actions{display:grid;grid-template-columns:1fr 1fr;gap:0.25rem;}
    .builder-block-actions button{width:28px;border:none;border-radius:7px;background:var(--slate-100);color:var(--slate-600);cursor:pointer;font-weight:900;}
    .builder-block-actions button:hover{background:var(--navy);color:#fff;}
    .inspector-panel{min-height:600px;}
    .inspector{padding:1.25rem;}
    .inspector-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;}
    .inspector-head span{font-size:0.68rem;font-weight:900;color:var(--sky-dark);text-transform:uppercase;letter-spacing:0.08em;}
    .inspector-head h3{font-family:var(--font-display);font-size:1.1rem;color:var(--navy);margin-top:2px;}
    .field-grid{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}
    .field-grid.two{grid-template-columns:repeat(4,minmax(130px,1fr));}
    .field.full,.image-field{grid-column:1/-1;}
    .toggle-field{display:flex;align-items:center;gap:0.65rem;padding:0.72rem 0.8rem;border:1.5px solid var(--slate-200);border-radius:8px;color:var(--slate-700);font-size:0.84rem;font-weight:800;background:var(--slate-50);}
    .toggle-field input{width:18px;height:18px;accent-color:var(--sky);}
    .color-field{display:grid;grid-template-columns:52px 1fr;gap:0.45rem;}
    .color-field span{grid-column:1/-1;}
    .color-field input[type=color]{padding:3px;height:39px;}
    .soft-sep{border:none;border-top:1px solid var(--slate-200);margin:1.25rem 0;}
    .image-row{display:flex;gap:0.5rem;align-items:center;}
    .upload-btn{position:relative;overflow:hidden;display:inline-flex;align-items:center;justify-content:center;min-width:78px;height:40px;border-radius:8px;background:var(--navy);color:#fff;font-size:0.78rem;font-weight:900;cursor:pointer;text-transform:none;letter-spacing:0;}
    .upload-btn input{position:absolute;inset:0;opacity:0;cursor:pointer;}
    .items-editor{grid-column:1/-1;margin:0.5rem 0 1rem;border:1px solid var(--slate-200);border-radius:12px;overflow:hidden;background:var(--slate-50);}
    .items-head{display:flex;align-items:center;justify-content:space-between;padding:0.85rem 1rem;border-bottom:1px solid var(--slate-200);background:#fff;}
    .items-head h4{font-size:0.86rem;color:var(--navy);}
    .item-card,.item-row{padding:0.85rem 1rem;border-bottom:1px solid var(--slate-200);display:grid;gap:0.55rem;}
    .item-card:last-child,.item-row:last-child{border-bottom:none;}
    .item-two,.item-row{display:grid;grid-template-columns:1fr 1fr auto;gap:0.55rem;align-items:center;}
    .item-card button,.item-row button{border:none;border-radius:8px;background:rgba(239,68,68,0.08);color:var(--danger);font-weight:900;padding:0.5rem 0.7rem;cursor:pointer;}
    .empty-builder{padding:2rem;text-align:center;color:var(--slate-400);}
    .toast{position:fixed;bottom:2rem;right:2rem;padding:1rem 1.5rem;border-radius:10px;font-weight:800;font-size:0.9rem;z-index:9999;display:none;}
    .toast.success{background:#D1FAE5;color:#065F46;}
    .toast.error{background:#FEE2E2;color:#991B1B;}
    @media(max-width:1100px){.builder-shell{grid-template-columns:1fr}.blocks-panel{position:relative;top:auto}.blocks-list{max-height:none}.field-grid.two{grid-template-columns:1fr 1fr}.page-settings{grid-template-columns:1fr}.status-toggle{justify-content:flex-start}}
    @media(max-width:760px){body{display:block}.main{margin-left:0}.topbar{height:auto;padding:1rem;align-items:flex-start;gap:1rem;flex-direction:column}.content{padding:1rem}.field-grid,.field-grid.two,.block-types,.item-two,.item-row{grid-template-columns:1fr}.topbar-actions{width:100%}.btn{flex:1}.saving-indicator{text-align:left}}
  </style>
</head>
<body>

<?php admin_sidebar('pages'); ?>

<div class="main">
  <div class="topbar">
    <h2><?= e($pageTitle) ?></h2>
    <div class="topbar-actions">
      <span class="saving-indicator" id="savingIndicator"></span>
      <?php if ($page && $page['slug']): ?>
        <a href="../pages/page.php?slug=<?= e($page['slug']) ?>" target="_blank" class="btn btn-outline">Visualizar</a>
      <?php endif; ?>
      <a href="pages.php" class="btn btn-outline">Voltar</a>
      <button class="btn btn-outline" onclick="savePage('draft')">
        <svg viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
        Salvar rascunho
      </button>
      <button class="btn btn-success" onclick="savePage('published')">
        <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        Publicar
      </button>
    </div>
  </div>

  <div class="content">
    <div class="page-settings">
      <div class="panel settings-panel">
        <label class="field" for="title">
          <span>Titulo da pagina *</span>
          <input type="text" id="title" placeholder="Ex: Sobre o CoBraLT" value="<?= e($page['title'] ?? ucfirst(str_replace('-', ' ', $presetSlug))) ?>">
        </label>
        <label class="field" for="slug" style="margin-top:1rem;">
          <span>Slug / URL</span>
          <input type="text" id="slug" placeholder="ex: historia" value="<?= e($page['slug'] ?? $presetSlug) ?>">
        </label>
      </div>
      <div class="status-toggle">
        <input type="radio" name="statusRadio" id="statusDraft" value="draft" <?= (!$page || $page['status'] === 'draft') ? 'checked' : '' ?>>
        <label for="statusDraft">Rascunho</label>
        <input type="radio" name="statusRadio" id="statusPublished" value="published" <?= ($page && $page['status'] === 'published') ? 'checked' : '' ?>>
        <label for="statusPublished">Publicada</label>
      </div>
    </div>

    <div class="builder-shell">
      <section class="panel blocks-panel">
        <div class="panel-head">
          <h3>Blocos da pagina</h3>
        </div>
        <div class="block-types">
          <button type="button" data-add-block="hero">Hero</button>
          <button type="button" data-add-block="rich_text">Texto</button>
          <button type="button" data-add-block="cards">Cards</button>
          <button type="button" data-add-block="image_text">Imagem + texto</button>
          <button type="button" data-add-block="stats">Numeros</button>
          <button type="button" data-add-block="cta">Chamada</button>
          <button type="button" data-add-block="gallery">Galeria</button>
          <button type="button" data-add-block="html">HTML</button>
        </div>
        <div class="blocks-list" id="blocksList"></div>
      </section>

      <section class="panel inspector-panel">
        <div class="panel-head">
          <h3>Personalizacao do bloco</h3>
        </div>
        <div class="inspector" id="blockInspector"></div>
      </section>
    </div>
  </div>
</div>

<div class="toast" id="toast"></div>

<script>
window.PAGE_ID = <?= $pageId ?: 'null' ?>;
window.CSRF = '<?= csrf_token() ?>';
window.PAGE_BUILDER_BLOCKS = <?= $blocksJson ?: '[]' ?>;
</script>
<script src="../js/admin-page-builder.js?v=1.0"></script>
</body>
</html>
