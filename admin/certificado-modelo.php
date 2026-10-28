<?php
/**
 * admin/certificado-modelo.php — Editor visual de um modelo de certificado.
 * Arraste os elementos sobre a arte; "Prévia PDF" gera um PDF de exemplo.
 */
declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
require_once dirname(__DIR__) . '/includes/certificate_issue.php';
require_role('superadmin', 'admin');

$db  = getDB();
cert_ensure_schema($db);
$tpl = cert_load_template($db, (int)($_GET['id'] ?? 0));
if (!$tpl) { header('Location: certificado-modelos.php'); exit; }

$signers = $db->query('SELECT * FROM certificate_signers ORDER BY name')->fetchAll();

function cert_tpl_signers(PDO $db, array $tpl): array {
    return [
        'sig1' => cert_load_signer($db, isset($tpl['signer1_id']) ? (int)$tpl['signer1_id'] : null),
        'sig2' => cert_load_signer($db, isset($tpl['signer2_id']) ? (int)$tpl['signer2_id'] : null),
    ];
}

if (isset($_GET['preview'])) {
    $sample = ['id' => 0, 'code' => 'CBL-AAAA-BBBB-CCCC', 'holder_name' => 'Maria da Silva Exemplo', 'event_name' => 'Nome do Evento de Exemplo',
        'cert_type' => 'participante', 'hours' => 8, 'issued_at' => date('Y-m-d')];
    $pdf = cert_render_template($sample, $tpl, cert_tpl_signers($db, $tpl));
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="previa-modelo.pdf"');
    echo $pdf;
    exit;
}

$errors = [];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $name = trim((string)($_POST['name'] ?? ''));
    $orientation = ($_POST['orientation'] ?? 'L') === 'P' ? 'P' : 'L';
    $body = trim((string)($_POST['body_text'] ?? ''));
    $s1 = (int)($_POST['signer1_id'] ?? 0) ?: null;
    $s2 = (int)($_POST['signer2_id'] ?? 0) ?: null;

    if (mb_strlen($name, 'UTF-8') < 3 || mb_strlen($name, 'UTF-8') > 160) $errors[] = 'Nome do modelo inválido.';
    if (mb_strlen($body, 'UTF-8') > 600) $errors[] = 'O texto do corpo pode ter até 600 caracteres.';

    // Layout: só aceita números/cores/alinhamentos válidos.
    $in = json_decode((string)($_POST['layout_json'] ?? ''), true);
    $layout = cert_default_layout();
    if (is_array($in)) {
        foreach ($layout as $key => $def) {
            foreach ($def as $prop => $dv) {
                $v = $in[$key][$prop] ?? null;
                if ($v === null) continue;
                if (in_array($prop, ['x', 'y', 'w'], true) && is_numeric($v)) $layout[$key][$prop] = max(0, min(100, round((float)$v, 2)));
                elseif ($prop === 'size' && is_numeric($v)) $layout[$key][$prop] = max(6, min(80, (float)$v));
                elseif ($prop === 'color' && preg_match('/^#[0-9a-f]{6}$/i', (string)$v)) $layout[$key][$prop] = (string)$v;
                elseif ($prop === 'align' && in_array($v, ['L', 'C', 'R'], true)) $layout[$key][$prop] = $v;
            }
        }
    }

    $bgFile = $tpl['background_file'];
    if (!$errors && !empty($_FILES['background']['name'])) {
        [$new, $err] = cert_save_image_upload($_FILES['background'], 'bg', 8 * 1024 * 1024);
        if ($err) { $errors[] = $err; }
        else {
            if ($old = cert_asset_path($bgFile)) @unlink($old);
            $bgFile = $new;
            $size = getimagesize((string)cert_asset_path($new));
            $orientation = ($size && $size[1] > $size[0]) ? 'P' : 'L'; // acompanha o formato da arte
        }
    }

    if (!$errors) {
        $db->prepare('UPDATE certificate_templates SET name=?, orientation=?, body_text=?, layout_json=?, signer1_id=?, signer2_id=?, background_file=? WHERE id=?')
           ->execute([$name, $orientation, $body ?: null, json_encode($layout), $s1, $s2, $bgFile, $tpl['id']]);
        $tpl = cert_load_template($db, (int)$tpl['id']);
        $msg = 'Modelo salvo.';
    }
}

$layout = array_replace_recursive(cert_default_layout(), json_decode((string)$tpl['layout_json'], true) ?: []);
$page = $tpl['orientation'] === 'P' ? ['w' => 210, 'h' => 297] : ['w' => 297, 'h' => 210];
$sigMap = [];
foreach ($signers as $sg) { $sigMap[$sg['id']] = ['name' => $sg['name'], 'role' => $sg['role'], 'image' => $sg['image_file']]; }
admin_page_start('Modelo: ' . $tpl['name'], 'modelos', 'Arraste os elementos sobre a arte e salve', '<a class="btn-sm outline" href="certificado-modelos.php">← Modelos</a>');
admin_alerts($msg, $errors);
?>
<style>
  .editor{display:grid;grid-template-columns:minmax(0,1fr) 340px;gap:1.5rem;align-items:start;} @media(max-width:1100px){.editor{grid-template-columns:1fr;}}
  #stage{position:relative;width:100%;background:#fff center/100% 100% no-repeat;border:1px solid #CBD5E1;box-shadow:0 2px 10px rgba(0,0,0,.08);overflow:hidden;touch-action:none;}
  .el{position:absolute;border:1.5px dashed #0EA5E9;background:rgba(14,165,233,.08);cursor:move;overflow:hidden;box-sizing:border-box;user-select:none;line-height:1.2;}
  .el.sel{border-style:solid;background:rgba(14,165,233,.2);} .el small{position:absolute;top:0;left:0;background:#0EA5E9;color:#fff;font-size:10px;padding:0 3px;}
  .el img{width:60%;margin:0 20%;display:block;}
  .row2{display:grid;grid-template-columns:1fr 1fr;gap:.75rem;} .field+.field,.row2+.field{margin-top:.8rem;}
</style>
<form method="post" enctype="multipart/form-data" id="f">
  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
  <input type="hidden" name="layout_json" id="layout_json">
  <input type="hidden" name="orientation" value="<?= e($tpl['orientation']) ?>">
  <div class="editor">
    <section class="card">
      <div class="card-header"><h3>Posicionar elementos</h3></div>
      <div class="card-body">
        <p class="hint" style="margin-bottom:1rem">Arraste cada caixa para o lugar certo sobre a arte. Clique numa caixa para ajustar largura, fonte e cor no painel ao lado. Depois use "Salvar" e "Prévia PDF".</p>
        <div id="stage" style="aspect-ratio:<?= $page['w'] ?>/<?= $page['h'] ?>;<?= ($bg = cert_asset_path($tpl['background_file'])) ? "background-image:url('certificado-modelos.php?asset=" . e($tpl['background_file']) . "')" : '' ?>"></div>
        <?php if (!$bg): ?><p class="hint" style="margin-top:.75rem">Sem arte de fundo ainda: envie uma imagem no painel ao lado.</p><?php endif; ?>
      </div>
    </section>
    <div>
      <section class="card">
        <div class="card-header"><h3>Modelo</h3></div>
        <div class="card-body">
          <div class="field"><label>Nome</label><input name="name" value="<?= e($tpl['name']) ?>" required maxlength="160"></div>
          <div class="field"><label>Arte de fundo (JPG/PNG, até 8 MB)</label><input type="file" name="background" accept="image/png,image/jpeg"></div>
          <p class="hint">A página (A4) acompanha o formato da imagem: horizontal ou vertical.</p>
          <div class="field"><label>Texto do certificado</label>
            <textarea name="body_text" rows="3" maxlength="600"><?= e((string)($tpl['body_text'] ?: cert_default_body())) ?></textarea></div>
          <p class="hint">Campos: {nome} {evento} {tipo} {tipo_frase} {horas} {horas_frase} {data}</p>
          <div class="row2">
            <div class="field"><label>Assinante 1</label><select name="signer1_id"><option value="0">—</option><?php foreach ($signers as $sg): ?><option value="<?= (int)$sg['id'] ?>" <?= (int)$tpl['signer1_id'] === (int)$sg['id'] ? 'selected' : '' ?>><?= e($sg['name']) ?></option><?php endforeach; ?></select></div>
            <div class="field"><label>Assinante 2</label><select name="signer2_id"><option value="0">—</option><?php foreach ($signers as $sg): ?><option value="<?= (int)$sg['id'] ?>" <?= (int)$tpl['signer2_id'] === (int)$sg['id'] ? 'selected' : '' ?>><?= e($sg['name']) ?></option><?php endforeach; ?></select></div>
          </div>
        </div>
      </section>
      <section class="card" id="props" hidden>
        <div class="card-header"><h3 id="pname"></h3></div>
        <div class="card-body"><div class="row2">
          <div class="field"><label>Largura (%)</label><input type="number" id="p_w" min="2" max="100" step="0.5"></div>
          <div class="field" id="l_size"><label>Fonte (pt)</label><input type="number" id="p_size" min="6" max="80" step="1"></div>
          <div class="field" id="l_color"><label>Cor</label><input type="color" id="p_color" style="padding:0;height:38px"></div>
          <div class="field" id="l_align"><label>Alinhamento</label><select id="p_align"><option value="L">Esquerda</option><option value="C">Centro</option><option value="R">Direita</option></select></div>
        </div></div>
      </section>
      <div class="btn-row" style="margin-top:0">
        <button type="submit" class="btn">Salvar modelo</button>
        <a class="btn outline" href="?id=<?= (int)$tpl['id'] ?>&amp;preview=1" target="_blank">Prévia PDF</a>
      </div>
      <p class="hint" style="margin-top:.5rem">A prévia usa o último modelo salvo.</p>
    </div>
  </div>
</form>

<script>
(function(){
  var PAGE_W = <?= $page['w'] ?>, PAGE_H = <?= $page['h'] ?>;
  var layout = <?= json_encode($layout, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  var signers = <?= json_encode($sigMap, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
  var chosen = {sig1: <?= (int)$tpl['signer1_id'] ?>, sig2: <?= (int)$tpl['signer2_id'] ?>};
  var labels = {name:'Nome', body:'Texto', sig1:'Assinatura 1', sig2:'Assinatura 2', qr:'QR code', code:'Código e link'};
  var hasText = {name:1, body:1, code:1};
  var stage = document.getElementById('stage'), els = {}, sel = null;

  function px() { return stage.clientWidth / PAGE_W; }           // px por mm
  function render(key) {
    var d = layout[key], e = els[key], k = px();
    e.style.left = d.x + '%'; e.style.top = d.y + '%'; e.style.width = d.w + '%';
    if (hasText[key]) {
      e.style.fontSize = (d.size * 0.3528 * k) + 'px'; e.style.color = d.color;
      e.style.textAlign = {L:'left', C:'center', R:'right'}[d.align];
    }
    if (key === 'qr') e.style.height = (PAGE_W * d.w / 100 * k) + 'px';
  }
  function build() {
    Object.keys(labels).forEach(function (key) {
      var e = document.createElement('div'); e.className = 'el'; e.dataset.key = key;
      var tag = '<small>' + labels[key] + '</small>';
      if (key === 'name') e.innerHTML = tag + '<div style="padding-top:12px"><b>NOME DA PESSOA</b></div>';
      else if (key === 'body') e.innerHTML = tag + '<div style="padding-top:12px">participou do evento Nome do Evento, com carga horária de 8 horas.</div>';
      else if (key === 'code') e.innerHTML = tag + '<div style="padding-top:12px">Código: CBL-XXXX-XXXX-XXXX<br>Verifique a autenticidade em …/verificar?c=…</div>';
      else if (key === 'qr') e.innerHTML = tag + '<div style="display:grid;place-items:center;height:100%;font-weight:700;color:#0284C7">QR</div>';
      else {
        var s = signers[chosen[key]];
        e.innerHTML = tag + '<div style="padding-top:12px">' + (s && s.image ? '<img src="certificado-modelos.php?asset=' + encodeURIComponent(s.image) + '" alt="">' : '<div style="height:30px"></div>') +
          '<div style="border-top:1px solid #555;text-align:center;font-size:11px;margin-top:2px"><b>' + (s ? esc(s.name) : 'Assinante') + '</b><br>' + (s && s.role ? esc(s.role) : '') + '</div></div>';
      }
      stage.appendChild(e); els[key] = e; render(key);
      e.addEventListener('pointerdown', function (ev) { select(key); drag(ev, key); });
    });
  }
  function esc(t){ return String(t).replace(/[&<>"']/g, function(c){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; }); }
  function drag(ev, key) {
    ev.preventDefault();
    var d = layout[key], sx = ev.clientX, sy = ev.clientY, ox = d.x, oy = d.y, r = stage.getBoundingClientRect();
    function move(m) {
      d.x = Math.max(0, Math.min(100 - d.w, ox + (m.clientX - sx) / r.width * 100));
      d.y = Math.max(0, Math.min(98, oy + (m.clientY - sy) / r.height * 100));
      d.x = Math.round(d.x * 100) / 100; d.y = Math.round(d.y * 100) / 100; render(key);
    }
    function up() { document.removeEventListener('pointermove', move); document.removeEventListener('pointerup', up); }
    document.addEventListener('pointermove', move); document.addEventListener('pointerup', up);
  }
  function select(key) {
    if (sel) els[sel].classList.remove('sel');
    sel = key; els[key].classList.add('sel');
    var d = layout[key], p = document.getElementById('props'); p.hidden = false;
    document.getElementById('pname').textContent = labels[key];
    document.getElementById('p_w').value = d.w;
    ['size', 'color', 'align'].forEach(function (f) {
      document.getElementById('l_' + f).style.display = hasText[key] ? '' : 'none';
      if (hasText[key]) document.getElementById('p_' + f).value = d[f];
    });
  }
  ['w', 'size', 'color', 'align'].forEach(function (f) {
    document.getElementById('p_' + f).addEventListener('input', function (ev) {
      if (!sel) return;
      var v = ev.target.value; layout[sel][f] = (f === 'color' || f === 'align') ? v : parseFloat(v) || layout[sel][f];
      render(sel);
    });
  });
  document.getElementById('f').addEventListener('submit', function () {
    document.getElementById('layout_json').value = JSON.stringify(layout);
  });
  window.addEventListener('resize', function () { Object.keys(els).forEach(render); });
  build();
})();
</script>
<?php admin_page_end(); ?>
