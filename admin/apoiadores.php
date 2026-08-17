<?php
declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
require_once dirname(__DIR__) . '/includes/supporters.php';

$db = getDB();
supporters_seed_if_empty($db);
$supporters = $db->query(
    'SELECT id, nome, instituicao, imagem, ativo, ordem
     FROM apoiadores ORDER BY ordem ASC, nome ASC'
)->fetchAll();
$activeCount = count(array_filter($supporters, static fn(array $item): bool => (int)$item['ativo'] === 1));
$canDelete = in_array($adminRole, ['superadmin', 'admin'], true);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Apoiadores — Admin CoBraLT</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    :root{--navy:#002B4E;--sky:#0EA5E9;--sky-dark:#0284C7;--success:#10B981;--danger:#EF4444;--slate-50:#F8FAFC;--slate-100:#F1F5F9;--slate-200:#E2E8F0;--slate-400:#94A3B8;--slate-600:#475569;--slate-800:#1E293B;--font-display:'Playfair Display',Georgia,serif;--font-body:'DM Sans','Segoe UI',sans-serif;--sidebar-w:240px}
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0} body{font-family:var(--font-body);background:var(--slate-100);color:var(--slate-800);display:flex;min-height:100vh}
    .sidebar{width:var(--sidebar-w);background:var(--navy);color:#fff;display:flex;flex-direction:column;position:fixed;inset:0 auto 0 0;z-index:100;overflow-y:auto}.sidebar-header{padding:1.5rem 1.25rem 1rem;border-bottom:1px solid rgba(255,255,255,.08);display:flex;align-items:center;gap:10px}.sidebar-logo-mark{width:36px;height:36px;background:rgba(14,165,233,.25);border-radius:8px;display:flex;align-items:center;justify-content:center}.sidebar-logo-mark svg,.sidebar-nav svg{fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round}.sidebar-logo-mark svg{width:18px;height:18px;color:#38BDF8}.sidebar-brand h1{font-family:var(--font-display);font-size:1rem}.sidebar-brand p{font-size:.7rem;color:rgba(255,255,255,.4)}
    .sidebar-nav{flex:1;padding:1rem .75rem;display:flex;flex-direction:column;gap:2px}.nav-label{font-size:.65rem;font-weight:700;color:rgba(255,255,255,.3);text-transform:uppercase;letter-spacing:.1em;padding:.75rem .75rem .25rem;margin-top:.5rem}.sidebar-nav a{display:flex;align-items:center;gap:10px;padding:.6rem .875rem;border-radius:8px;color:rgba(255,255,255,.62);text-decoration:none;font-size:.9rem;font-weight:500}.sidebar-nav a:hover{background:rgba(255,255,255,.08);color:#fff}.sidebar-nav a.active{background:rgba(14,165,233,.2);color:#38BDF8}.sidebar-nav svg{width:16px;height:16px}.sidebar-footer{padding:1rem .75rem;border-top:1px solid rgba(255,255,255,.08)}.user-info{padding:.75rem;border-radius:8px;background:rgba(255,255,255,.05);margin-bottom:.75rem;min-width:0}.user-name{font-size:.85rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.user-role{font-size:.7rem;color:rgba(255,255,255,.4);margin-top:2px}.btn-logout{display:flex;justify-content:center;width:100%;padding:.6rem;color:#FCA5A5;border:1px solid rgba(239,68,68,.2);border-radius:8px;text-decoration:none;font-size:.85rem}
    .main{margin-left:var(--sidebar-w);flex:1;min-width:0}.topbar{height:60px;padding:0 2rem;background:#fff;border-bottom:1px solid var(--slate-200);display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:50}.topbar h2{font-family:var(--font-display);font-size:1.2rem;color:var(--navy)}.topbar span{font-size:.78rem;color:var(--slate-400)}.content{padding:2rem}.summary{display:flex;gap:1rem;flex-wrap:wrap;margin-bottom:1.5rem}.summary-card{background:#fff;border:1px solid var(--slate-200);border-radius:12px;padding:1rem 1.25rem;min-width:150px}.summary-card strong{font-family:var(--font-display);font-size:1.6rem;color:var(--navy);display:block}.summary-card span{font-size:.72rem;color:var(--slate-400);text-transform:uppercase;letter-spacing:.06em}
    .layout{display:grid;grid-template-columns:minmax(300px,390px) minmax(0,1fr);gap:1.5rem;align-items:start}.card{background:#fff;border:1px solid var(--slate-200);border-radius:12px;overflow:hidden}.card-header{padding:1.1rem 1.25rem;border-bottom:1px solid var(--slate-200);display:flex;align-items:center;justify-content:space-between;gap:1rem}.card-header h3{font-size:.95rem;color:var(--navy)}.card-body{padding:1.25rem}.form-group{margin-bottom:1rem}.form-group label{display:block;font-size:.78rem;font-weight:700;color:var(--slate-600);margin-bottom:.4rem}.form-group input[type=text],.form-group input[type=number]{width:100%;padding:.72rem .8rem;border:1px solid var(--slate-200);border-radius:8px;font:inherit;font-size:.88rem}.form-group input:focus{outline:none;border-color:var(--sky);box-shadow:0 0 0 3px rgba(14,165,233,.1)}.check-row{display:flex;align-items:center;gap:.55rem;font-size:.86rem;color:var(--slate-600);margin:.25rem 0 1rem}.check-row input{width:17px;height:17px;accent-color:var(--sky)}
    .preview{aspect-ratio:9/16;max-height:330px;background:var(--slate-100);border:1px dashed var(--slate-200);border-radius:10px;overflow:hidden;display:flex;align-items:center;justify-content:center;margin-bottom:.75rem}.preview img{width:100%;height:100%;object-fit:cover;object-position:top}.preview-placeholder{text-align:center;color:var(--slate-400);font-size:.8rem;padding:1rem}.upload-label{display:flex;justify-content:center;padding:.7rem;border:1px solid var(--slate-200);border-radius:8px;color:var(--navy);font-size:.82rem;font-weight:700;cursor:pointer;background:var(--slate-50)}.upload-label:hover{border-color:var(--sky);color:var(--sky-dark)}#imageFile{display:none}.actions{display:flex;gap:.65rem;margin-top:1.15rem}.btn{border:0;border-radius:8px;padding:.68rem 1rem;font:inherit;font-size:.82rem;font-weight:700;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;justify-content:center}.btn-primary{background:var(--navy);color:#fff;flex:1}.btn-primary:hover{background:var(--sky-dark)}.btn-outline{background:#fff;color:var(--slate-600);border:1px solid var(--slate-200)}.btn-danger{background:#fff;color:var(--danger);border:1px solid #FECACA}.btn-sm{padding:.45rem .7rem;font-size:.75rem}
    .table-wrap{overflow:auto;max-height:calc(100vh - 210px)}table{width:100%;border-collapse:collapse;min-width:680px}th{position:sticky;top:0;background:var(--slate-100);z-index:2;padding:.7rem .85rem;text-align:left;font-size:.68rem;text-transform:uppercase;color:var(--slate-400);letter-spacing:.06em}td{padding:.7rem .85rem;border-top:1px solid var(--slate-200);font-size:.82rem;vertical-align:middle}.thumb{width:46px;height:66px;border-radius:6px;object-fit:cover;object-position:top;background:var(--slate-100)}.supporter-name{font-weight:700;color:var(--navy)}.supporter-inst{color:var(--slate-400);font-size:.72rem;max-width:240px}.badge{display:inline-flex;padding:3px 8px;border-radius:999px;font-size:.68rem;font-weight:700}.badge.active{background:#D1FAE5;color:#065F46}.badge.inactive{background:#FEE2E2;color:#991B1B}.row-actions{display:flex;gap:.4rem}.empty{padding:2rem;text-align:center;color:var(--slate-400)}.toast{position:fixed;right:1.5rem;bottom:1.5rem;padding:.85rem 1rem;border-radius:9px;background:#D1FAE5;color:#065F46;font-size:.82rem;font-weight:700;display:none;z-index:999}.toast.error{background:#FEE2E2;color:#991B1B}
    @media(max-width:980px){.layout{grid-template-columns:1fr}.table-wrap{max-height:none}}@media(max-width:768px){.sidebar{position:static;width:100%;min-height:auto}.sidebar-nav{display:grid;grid-template-columns:repeat(2,1fr)}.sidebar-footer{display:none}.main{margin-left:0}.topbar{padding:0 1rem}.content{padding:1rem}body{display:block}}
  </style>
</head>
<body>
<aside class="sidebar">
  <div class="sidebar-header"><div class="sidebar-logo-mark"><svg viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg></div><div class="sidebar-brand"><h1>CoBraLT</h1><p>Painel Admin</p></div></div>
  <nav class="sidebar-nav" aria-label="Menu admin">
    <div class="nav-label">Geral</div>
    <a href="dashboard.php"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>Dashboard</a>
    <div class="nav-label">Conteúdo</div>
    <a href="posts.php"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>Notícias</a>
    <a href="pages.php"><svg viewBox="0 0 24 24"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>Páginas</a>
    <a href="apoiadores.php" class="active"><svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6M23 11h-6"/></svg>Apoiadores</a>
    <div class="nav-label">Sistema</div>
    <a href="usuarios.php"><svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>Usuários</a>
    <a href="../pages/apoiadores" target="_blank"><svg viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>Ver página</a>
  </nav>
  <div class="sidebar-footer"><div class="user-info"><div class="user-name"><?= e((string)$adminUser) ?></div><div class="user-role"><?= e((string)$adminRole) ?></div></div><a href="../api/logout.php" class="btn-logout">Sair</a></div>
</aside>

<main class="main">
  <header class="topbar"><h2>Gerenciar apoiadores</h2><span>As alterações aparecem na home e na página de apoiadores</span></header>
  <div class="content">
    <div class="summary"><div class="summary-card"><strong><?= count($supporters) ?></strong><span>cadastrados</span></div><div class="summary-card"><strong><?= $activeCount ?></strong><span>visíveis no site</span></div></div>
    <div class="layout">
      <section class="card" aria-labelledby="formTitle">
        <div class="card-header"><h3 id="formTitle">Adicionar apoiador</h3><button type="button" class="btn btn-outline btn-sm" id="newButton" hidden>Novo</button></div>
        <div class="card-body">
          <input type="hidden" id="supporterId">
          <div class="form-group"><label for="nome">Nome *</label><input type="text" id="nome" maxlength="180" placeholder="Nome completo"></div>
          <div class="form-group"><label for="instituicao">Instituição ou liga</label><input type="text" id="instituicao" maxlength="255" placeholder="Opcional"></div>
          <div class="form-group"><label>Card do apoiador *</label><div class="preview" id="preview"><div class="preview-placeholder">Envie a arte vertical do apoiador</div></div><label class="upload-label" for="imageFile">Selecionar JPG, PNG ou WebP</label><input type="file" id="imageFile" accept="image/jpeg,image/png,image/webp"><input type="hidden" id="imagem"></div>
          <div class="form-group"><label for="ordem">Ordem de exibição</label><input type="number" id="ordem" min="0" max="999999" step="10" value="0"><small style="color:var(--slate-400);font-size:.7rem">Use 0 para adicionar ao final.</small></div>
          <label class="check-row"><input type="checkbox" id="ativo" checked>Exibir no site</label>
          <div class="actions"><button type="button" class="btn btn-primary" id="saveButton">Salvar apoiador</button><button type="button" class="btn btn-outline" id="cancelButton" hidden>Cancelar</button></div>
        </div>
      </section>

      <section class="card" aria-labelledby="listTitle">
        <div class="card-header"><h3 id="listTitle">Apoiadores cadastrados</h3><span style="font-size:.72rem;color:var(--slate-400)">Edite a ordem para reorganizar o carrossel</span></div>
        <?php if (!$supporters): ?><div class="empty">Nenhum apoiador cadastrado.</div><?php else: ?>
        <div class="table-wrap"><table><thead><tr><th>Card</th><th>Apoiador</th><th>Ordem</th><th>Status</th><th>Ações</th></tr></thead><tbody>
        <?php foreach ($supporters as $supporter): ?>
          <tr id="row-<?= (int)$supporter['id'] ?>">
            <td><img class="thumb" src="<?= e(supporter_image_src((string)$supporter['imagem'], '../')) ?>" alt=""></td>
            <td><div class="supporter-name"><?= e((string)$supporter['nome']) ?></div><?php if (!empty($supporter['instituicao'])): ?><div class="supporter-inst"><?= e((string)$supporter['instituicao']) ?></div><?php endif; ?></td>
            <td><?= (int)$supporter['ordem'] ?></td>
            <td><span class="badge <?= (int)$supporter['ativo'] === 1 ? 'active' : 'inactive' ?>"><?= (int)$supporter['ativo'] === 1 ? 'Visível' : 'Oculto' ?></span></td>
            <td><div class="row-actions"><button type="button" class="btn btn-outline btn-sm" onclick="editSupporter(<?= (int)$supporter['id'] ?>)">Editar</button><?php if ($canDelete): ?><button type="button" class="btn btn-danger btn-sm" onclick='deleteSupporter(<?= (int)$supporter['id'] ?>, <?= json_encode((string)$supporter['nome'], JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>Excluir</button><?php endif; ?></div></td>
          </tr>
        <?php endforeach; ?>
        </tbody></table></div><?php endif; ?>
      </section>
    </div>
  </div>
</main>
<div class="toast" id="toast"></div>
<script>
const CSRF = <?= json_encode(csrf_token()) ?>;
const apiUrl = '../api/supporters.php';
const uploadUrl = '../api/upload.php';
const fields = {id:document.getElementById('supporterId'),nome:document.getElementById('nome'),instituicao:document.getElementById('instituicao'),imagem:document.getElementById('imagem'),ordem:document.getElementById('ordem'),ativo:document.getElementById('ativo')};
const preview = document.getElementById('preview');

function toast(message, type='success'){const el=document.getElementById('toast');el.textContent=message;el.className='toast '+type;el.style.display='block';clearTimeout(window.toastTimer);window.toastTimer=setTimeout(()=>el.style.display='none',3200)}
function setPreview(src){preview.innerHTML=src?'<img src="'+src.replaceAll('"','&quot;')+'" alt="Prévia do card">':'<div class="preview-placeholder">Envie a arte vertical do apoiador</div>'}
function resetForm(){fields.id.value='';fields.nome.value='';fields.instituicao.value='';fields.imagem.value='';fields.ordem.value='0';fields.ativo.checked=true;setPreview('');document.getElementById('formTitle').textContent='Adicionar apoiador';document.getElementById('cancelButton').hidden=true;document.getElementById('newButton').hidden=true}
document.getElementById('cancelButton').addEventListener('click',resetForm);document.getElementById('newButton').addEventListener('click',resetForm);
document.getElementById('imageFile').addEventListener('change',async event=>{const file=event.target.files[0];if(!file)return;const fd=new FormData();fd.append('image',file);fd.append('csrf_token',CSRF);toast('Enviando imagem...');try{const response=await fetch(uploadUrl,{method:'POST',body:fd});const data=await response.json();if(!data.success)throw new Error(data.message||'Falha no upload.');const storedPath=data.url.replace(/^\//,'');fields.imagem.value=storedPath;setPreview('../'+storedPath);toast('Imagem enviada. Agora salve o apoiador.')}catch(error){toast(error.message,'error')}});

async function editSupporter(id){try{const response=await fetch(apiUrl+'?action=get&id='+encodeURIComponent(id));const data=await response.json();if(!data.success)throw new Error(data.message);const item=data.supporter;fields.id.value=item.id;fields.nome.value=item.nome||'';fields.instituicao.value=item.instituicao||'';fields.imagem.value=item.imagem||'';fields.ordem.value=item.ordem||0;fields.ativo.checked=Number(item.ativo)===1;setPreview('../'+String(item.imagem).replace(/^\//,''));document.getElementById('formTitle').textContent='Editar apoiador';document.getElementById('cancelButton').hidden=false;document.getElementById('newButton').hidden=false;window.scrollTo({top:0,behavior:'smooth'})}catch(error){toast(error.message||'Não foi possível carregar o apoiador.','error')}}

document.getElementById('saveButton').addEventListener('click',async()=>{if(!fields.nome.value.trim()){toast('Informe o nome do apoiador.','error');return}if(!fields.imagem.value){toast('Selecione a imagem do apoiador.','error');return}const fd=new FormData();fd.append('action',fields.id.value?'update':'create');fd.append('csrf_token',CSRF);fd.append('id',fields.id.value);fd.append('nome',fields.nome.value.trim());fd.append('instituicao',fields.instituicao.value.trim());fd.append('imagem',fields.imagem.value);fd.append('ordem',fields.ordem.value||'0');if(fields.ativo.checked)fd.append('ativo','1');const button=document.getElementById('saveButton');button.disabled=true;try{const response=await fetch(apiUrl,{method:'POST',body:fd});const data=await response.json();if(!data.success)throw new Error(data.message);toast(fields.id.value?'Apoiador atualizado!':'Apoiador adicionado!');setTimeout(()=>location.reload(),700)}catch(error){toast(error.message||'Não foi possível salvar.','error')}finally{button.disabled=false}});

async function deleteSupporter(id,name){if(!confirm('Excluir permanentemente '+name+'? A imagem continuará armazenada para segurança.'))return;const fd=new FormData();fd.append('action','delete');fd.append('id',id);fd.append('csrf_token',CSRF);try{const response=await fetch(apiUrl,{method:'POST',body:fd});const data=await response.json();if(!data.success)throw new Error(data.message);document.getElementById('row-'+id)?.remove();toast('Apoiador excluído.')}catch(error){toast(error.message||'Não foi possível excluir.','error')}}
</script>
</body>
</html>
