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
                elseif ($prop === 'font' && in_array($v, ['Helvetica', 'Times', 'Courier'], true)) $layout[$key][$prop] = $v;
                elseif ($prop === 'style' && in_array($v, ['', 'B', 'I', 'BI'], true)) $layout[$key][$prop] = $v;
            }
        }
    }

    if (is_array($in)) {
        $n = 0;
        foreach ($in as $key => $el) {
            if (preg_match('/^extra\d{1,2}$/', (string)$key) && is_array($el) && ++$n <= 12) {
                $layout[$key] = cert_clean_text_box($el);
            }
        }
    }

    $bgFile = $tpl['background_file'];
    if (!$errors && !empty($_FILES['background']['name'])) {
        [$new, $newOrientation, $err] = cert_save_background($_FILES['background']);
        if ($err) { $errors[] = $err; }
        else {
            if ($old = cert_asset_path($bgFile)) @unlink($old);
            if ($bgFile && ($oldPrev = cert_asset_path(cert_preview_name($bgFile)))) @unlink($oldPrev);
            $bgFile = $new;
            $orientation = $newOrientation; // acompanha o formato do fundo
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
$bgFile = $tpl['background_file'];
$bgPath = cert_asset_path($bgFile);
$bgIsPdf = $bgPath !== null && str_ends_with($bgPath, '.pdf');
$prevFile = $bgIsPdf ? cert_preview_name((string)$bgFile) : null;
$cfg = [
    'pageW' => $page['w'], 'pageH' => $page['h'],
    'layout' => $layout,
    'bodyText' => (string)($tpl['body_text'] ?: cert_default_body()),
    'signers' => $sigMap,
    'chosen' => ['sig1' => (int)$tpl['signer1_id'], 'sig2' => (int)$tpl['signer2_id']],
    'bgPdf' => $bgIsPdf ? 'certificado-modelos.php?asset=' . $bgFile : null,
    'bgName' => $bgIsPdf ? $bgFile : null,
    'previewUrl' => ($bgIsPdf && cert_asset_path($prevFile)) ? 'certificado-modelos.php?asset=' . $prevFile : null,
    'csrf' => csrf_token(),
    // chave usada no texto, nome da etiqueta, descrição no menu, exemplo mostrado no certificado
    'fields' => [
        ['key' => 'nome', 'chip' => 'Nome', 'label' => 'Nome da pessoa', 'sample' => 'Maria da Silva Santos'],
        ['key' => 'evento', 'chip' => 'Evento', 'label' => 'Nome do evento', 'sample' => 'Nome do Evento'],
        ['key' => 'tipo', 'chip' => 'Tipo', 'label' => 'Tipo (Participante, Palestrante…)', 'sample' => 'Participante'],
        ['key' => 'tipo_frase', 'chip' => 'Frase do tipo', 'label' => 'Tipo em frase (participou do evento…)', 'sample' => 'participou do evento'],
        ['key' => 'horas', 'chip' => 'Horas', 'label' => 'Carga horária (só o número)', 'sample' => '8'],
        ['key' => 'horas_frase', 'chip' => 'Frase das horas', 'label' => 'Carga horária em frase (, com carga horária de 8 horas)', 'sample' => ', com carga horária de 8 horas'],
        ['key' => 'data', 'chip' => 'Data', 'label' => 'Data do certificado', 'sample' => date('d/m/Y')],
        ['key' => 'codigo', 'chip' => 'Código', 'label' => 'Código do certificado', 'sample' => 'CBL-XXXX-XXXX-XXXX'],
        ['key' => 'link', 'chip' => 'Link', 'label' => 'Link de verificação', 'sample' => 'cobralt.com.br/verificar?c=…'],
    ],
];
admin_page_start('Modelo: ' . $tpl['name'], 'modelos', 'Clique no certificado para editar — o que você vê é o que sai no PDF', '<a class="btn-sm outline" href="certificado-modelos.php">← Modelos</a>');
admin_alerts($msg, $errors);
?>
<style>
  .editor{display:grid;grid-template-columns:minmax(0,1fr) 300px;gap:1.5rem;align-items:start;} @media(max-width:1100px){.editor{grid-template-columns:1fr;}}
  .tbar{display:flex;flex-wrap:wrap;gap:.4rem;align-items:center;padding:.5rem;background:var(--slate-100);border:1px solid var(--slate-200);border-radius:10px;margin-bottom:.5rem;position:relative;}
  .tbar select,.tbar input[type=number]{width:auto;padding:.35rem .5rem;font-size:.85rem;} .tbar input[type=number]{width:64px;text-align:center;}
  .tbar input[type=color]{width:36px;height:32px;padding:2px;cursor:pointer;}
  .tbar button{min-width:34px;height:32px;padding:0 .6rem;border:1.5px solid var(--slate-200);background:#fff;border-radius:8px;cursor:pointer;font:inherit;font-size:.85rem;color:var(--navy);}
  .tbar button:hover:not(:disabled){background:#E0F2FE;} .tbar button.on{background:var(--navy);color:#fff;border-color:var(--navy);}
  .tbar :disabled{opacity:.4;cursor:not-allowed;} .tbar .sep{width:1px;height:24px;background:var(--slate-200);margin:0 .2rem;}
  .tbar .primary{background:#0EA5E9;color:#fff;border-color:#0EA5E9;font-weight:600;} .tbar .primary:hover:not(:disabled){background:#0284C7;}
  #fieldMenu{position:absolute;top:100%;left:.5rem;z-index:30;background:#fff;border:1px solid var(--slate-200);border-radius:10px;box-shadow:0 10px 30px rgba(0,43,78,.18);padding:.35rem;width:340px;max-width:92vw;}
  #fieldMenu[hidden]{display:none;} #fieldMenu button{display:flex;width:100%;height:auto;justify-content:space-between;gap:.75rem;padding:.5rem .65rem;border:0;text-align:left;border-radius:6px;}
  #fieldMenu small{color:var(--slate-400);font-size:.75rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:130px;}
  #stage{position:relative;width:100%;background:#fff center/100% 100% no-repeat;border:1px solid #CBD5E1;box-shadow:0 2px 10px rgba(0,0,0,.08);overflow:hidden;touch-action:none;user-select:none;}
  .el{position:absolute;box-sizing:border-box;cursor:move;border:1.5px dashed transparent;border-radius:2px;}
  .el:hover{border-color:#7DD3FC;} .el.sel{border:1.5px solid #0EA5E9;background:rgba(14,165,233,.06);}
  .el.editing{cursor:text;background:rgba(255,255,255,.9);box-shadow:0 0 0 3px rgba(14,165,233,.25);user-select:text;}
  .el .c{min-height:1em;outline:none;word-wrap:break-word;} .el .c .ph{color:#94A3B8;font-style:italic;}
  .el .tag{display:none;position:absolute;left:-1.5px;bottom:100%;background:#0EA5E9;color:#fff;font:600 10px/1 'DM Sans',sans-serif;padding:3px 6px;border-radius:4px 4px 0 0;white-space:nowrap;pointer-events:none;}
  .el:hover .tag,.el.sel .tag{display:block;}
  .el .hr{display:none;position:absolute;right:-6px;top:50%;width:10px;height:26px;margin-top:-13px;background:#0EA5E9;border-radius:4px;cursor:ew-resize;box-shadow:0 1px 4px rgba(0,0,0,.3);}
  .el.sel .hr{display:block;}
  .el .c img{width:60%;margin:0 20%;display:block;} .el .sigl{border-top:1px solid #555;text-align:center;font-size:11px;margin-top:2px;line-height:1.3;color:#1E293B;}
  .qrph{width:100%;height:100%;background:repeating-conic-gradient(#002B4E 0 25%,#fff 0 50%) 0 0/16.66% 16.66%;border:3px solid #fff;outline:1px solid #002B4E;box-sizing:border-box;}
  .el[data-key=qr] .c{height:100%;}
  .chip{display:inline-block;background:#0EA5E9;color:#fff;font:600 .72em/1.5 'DM Sans',sans-serif;padding:0 .5em;border-radius:99px;margin:0 1px;vertical-align:baseline;user-select:all;}
  .row2{display:grid;grid-template-columns:1fr 1fr;gap:.75rem;} .field+.field,.row2+.field{margin-top:.8rem;}
</style>
<form method="post" enctype="multipart/form-data" id="f">
  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
  <input type="hidden" name="layout_json" id="layout_json">
  <input type="hidden" name="body_text" id="body_text">
  <input type="hidden" name="orientation" value="<?= e($tpl['orientation']) ?>">
  <div class="editor">
    <section class="card">
      <div class="card-header">
        <h3>Certificado</h3>
        <div class="actions">
          <button type="button" class="btn-sm outline" id="addExtra">＋ Caixa de texto</button>
          <a class="btn-sm outline" href="?id=<?= (int)$tpl['id'] ?>&amp;preview=1" target="_blank">Prévia PDF</a>
          <button type="submit" class="btn-sm">Salvar modelo</button>
        </div>
      </div>
      <div class="card-body">
        <div class="tbar" id="tbar">
          <select id="t_font" title="Fonte"><option value="Helvetica">Sem serifa</option><option value="Times">Clássica</option><option value="Courier">Máquina</option></select>
          <button type="button" id="t_minus" title="Diminuir">−</button><input type="number" id="t_size" min="6" max="80" step="1" title="Tamanho"><button type="button" id="t_plus" title="Aumentar">＋</button>
          <input type="color" id="t_color" title="Cor">
          <button type="button" id="t_b" title="Negrito"><b>B</b></button><button type="button" id="t_i" title="Itálico"><i>I</i></button>
          <span class="sep"></span>
          <button type="button" id="t_l" title="Alinhar à esquerda">⯇</button><button type="button" id="t_c" title="Centralizar">≡</button><button type="button" id="t_r" title="Alinhar à direita">⯈</button>
          <span class="sep"></span>
          <button type="button" id="t_field" class="primary" title="Coloca no texto algo que muda em cada certificado">＋ Inserir campo ▾</button>
          <button type="button" id="t_del" title="Remover esta caixa de texto">Excluir caixa</button>
          <div id="fieldMenu" hidden>
            <?php foreach ($cfg['fields'] as $fd): ?>
              <button type="button" data-f="<?= e($fd['key']) ?>"><span><?= e($fd['label']) ?></span><small><?= e($fd['sample']) ?></small></button>
            <?php endforeach; ?>
          </div>
        </div>
        <p class="hint" id="t_hint" style="margin:0 0 .75rem"></p>
        <p class="hint" id="bgStatus" style="margin:0 0 .5rem" hidden></p>
        <div id="stage" style="aspect-ratio:<?= $page['w'] ?>/<?= $page['h'] ?>;<?= ($bgPath && !$bgIsPdf) ? "background-image:url('certificado-modelos.php?asset=" . e((string)$bgFile) . "')" : '' ?>"></div>
        <?php if (!$bgPath): ?><p class="hint" style="margin-top:.75rem">Sem certificado de fundo ainda: envie o PDF ou a imagem no painel ao lado.</p><?php endif; ?>
      </div>
    </section>
    <div>
      <section class="card">
        <div class="card-header"><h3>Modelo</h3></div>
        <div class="card-body">
          <div class="field"><label>Nome do modelo</label><input name="name" value="<?= e($tpl['name']) ?>" required maxlength="160"></div>
          <div class="field"><label>Trocar o certificado de fundo</label><input type="file" name="background" accept="application/pdf,image/png,image/jpeg,.pdf"></div>
          <p class="hint">PDF, JPG ou PNG de até 8 MB. O desenho e as assinaturas que já estão nele aparecem como no original.</p>
          <div class="row2">
            <div class="field"><label>Assinante extra 1</label><select name="signer1_id"><option value="0">—</option><?php foreach ($signers as $sg): ?><option value="<?= (int)$sg['id'] ?>" <?= (int)$tpl['signer1_id'] === (int)$sg['id'] ? 'selected' : '' ?>><?= e($sg['name']) ?></option><?php endforeach; ?></select></div>
            <div class="field"><label>Assinante extra 2</label><select name="signer2_id"><option value="0">—</option><?php foreach ($signers as $sg): ?><option value="<?= (int)$sg['id'] ?>" <?= (int)$tpl['signer2_id'] === (int)$sg['id'] ? 'selected' : '' ?>><?= e($sg['name']) ?></option><?php endforeach; ?></select></div>
          </div>
          <p class="hint">Só use se as assinaturas não estiverem no fundo.</p>
        </div>
      </section>
      <section class="card">
        <div class="card-header"><h3>Como usar</h3></div>
        <div class="card-body hint">
          <p><strong>Clique</strong> numa caixa para escrever direto no certificado.</p>
          <p style="margin-top:.5rem"><strong>Arraste</strong> para mover e puxe a <strong>alça azul</strong> para a largura.</p>
          <p style="margin-top:.5rem">Em <strong>＋ Inserir campo</strong> escolha o que muda em cada certificado: nome, evento, horas, data… Eles aparecem como etiquetas azuis e são preenchidos pela planilha.</p>
          <p style="margin-top:.5rem">Use <strong>＋ Caixa de texto</strong> para qualquer outro texto (ex.: "Carga horária: [Horas] h").</p>
        </div>
      </section>
    </div>
  </div>
</form>
<script id="cfg" type="application/json"><?= json_encode($cfg, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
<script src="../js/vendor/pdfjs/pdf.min.js"></script>
<script src="../js/modelo-editor.js?v=<?= (int)@filemtime(dirname(__DIR__) . '/js/modelo-editor.js') ?>"></script>
<?php admin_page_end(); ?>
