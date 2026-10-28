<?php
/**
 * admin/certificado-assinar-pdf.php — Assina um certificado PDF já pronto:
 * registra no banco e carimba QR, código, link e assinatura onde o admin posicionar (arrastando na prévia).
 * O PDF carimbado é o que vale na verificação.
 */
declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
require_once dirname(__DIR__) . '/includes/certificate_issue.php';
require_role('superadmin', 'admin');

$db = getDB();
$signers = $db->query('SELECT * FROM certificate_signers ORDER BY name')->fetchAll();
$errors = [];
$done = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $f = $_FILES['pdf'] ?? null;
    $signer = cert_load_signer($db, (int)($_POST['signer_id'] ?? 0) ?: null);
    $pos = [
        'page' => !empty($_POST['all_pages']) ? 0 : (int)($_POST['stamp_page'] ?? 1),
        'x' => $_POST['stamp_x'] ?? 70, 'y' => $_POST['stamp_y'] ?? 85, 'w' => $_POST['stamp_w'] ?? 22,
    ];
    [$data, $err] = cert_validate($_POST);

    if (!$f || $f['error'] !== UPLOAD_ERR_OK || $f['size'] > 8 * 1024 * 1024) {
        $errors[] = 'Envie um PDF de até 8 MB.';
    } elseif (!str_starts_with((string)file_get_contents($f['tmp_name'], false, null, 0, 5), '%PDF-')) {
        $errors[] = 'O arquivo não é um PDF.';
    } elseif ($err) {
        $errors[] = $err;
    } else {
        $db->beginTransaction();
        try {
            $cert = cert_insert($db, $data, $adminId);
            [$bytes, $stampErr] = cert_stamp_pdf($f['tmp_name'], $cert, $signer, $pos);
            if ($bytes === null) {
                $db->rollBack();
                $errors[] = $stampErr;
            } else {
                $done = cert_store_pdf($db, $cert, $bytes, 'pdf');
                $db->commit();
            }
        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            error_log('[CoBraLT] assinar pdf: ' . $e->getMessage());
            $errors[] = 'Erro ao assinar o PDF. Nada foi registrado.';
        }
    }
}

$sigMap = [];
foreach ($signers as $sg) { $sigMap[$sg['id']] = ['name' => $sg['name'], 'role' => $sg['role'], 'image' => $sg['image_file']]; }

admin_page_start('Assinar PDF pronto', 'assinar-pdf', 'Envie o certificado final e posicione o selo de verificação');
?>
<?php if ($done): ?>
  <div class="alert ok">
    <strong>Certificado registrado e assinado:</strong> <span class="mono"><?= e($done['code']) ?></span><br>
    <a href="certificados.php?pdf=<?= (int)$done['id'] ?>">Baixar o PDF assinado</a> ·
    <a href="autenticar.php?c=<?= e(rawurlencode((string)$done['code'])) ?>">Conferir</a><br>
    <small>Entregue <strong>este</strong> arquivo: a verificação por PDF compara com ele, byte a byte.</small>
  </div>
<?php endif; ?>
<?php admin_alerts('', $errors); ?>

<style>
  .sign-layout{display:grid;grid-template-columns:minmax(0,1fr) 440px;gap:1.5rem;align-items:start;}
  @media(max-width:1100px){.sign-layout{grid-template-columns:1fr;}}
  .pv-wrap{position:relative;width:100%;max-width:440px;margin:0 auto;background:#E2E8F0;border:1px solid var(--slate-200);border-radius:8px;overflow:hidden;touch-action:none;user-select:none;}
  .pv-wrap canvas{display:block;width:100%;height:auto;}
  .pv-empty{padding:3rem 1rem;text-align:center;color:var(--slate-400);font-size:.9rem;}
  .stamp{position:absolute;border:2px solid #0EA5E9;background:rgba(255,255,255,.93);cursor:move;display:flex;gap:4%;align-items:center;padding:1.5%;box-shadow:0 2px 8px rgba(0,43,78,.3);}
  .stamp .qr{flex:0 0 28%;aspect-ratio:1;background:repeating-conic-gradient(#002B4E 0 25%,#fff 0 50%) 0 0/25% 25%;border:1px solid #002B4E;}
  .stamp .tx{line-height:1.15;color:#002B4E;min-width:0;overflow:hidden;} .stamp img{display:block;}
  .pv-nav{display:flex;align-items:center;justify-content:center;gap:.75rem;margin-top:.75rem;font-size:.85rem;}
  .range-row{display:flex;gap:.75rem;align-items:center;} .range-row input{flex:1;padding:0;}
  .row2{display:grid;grid-template-columns:1fr 1fr;gap:.75rem;}
</style>

<form method="post" enctype="multipart/form-data" id="signForm">
  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
  <input type="hidden" name="stamp_page" id="stamp_page" value="1">
  <input type="hidden" name="stamp_x" id="stamp_x" value="70">
  <input type="hidden" name="stamp_y" id="stamp_y" value="85">
  <input type="hidden" name="stamp_w" id="stamp_w" value="22">
  <div class="sign-layout">
    <section class="card">
      <div class="card-header"><h3>Dados do certificado</h3></div>
      <div class="card-body">
        <p class="hint" style="margin-bottom:1rem">Envie o PDF final (feito no Canva, Word etc.). O sistema registra no banco, gera o código e carimba um selo com <strong>QR code, código, link e assinatura</strong> na posição que você escolher na prévia.</p>
        <div class="grid-form">
          <div class="field"><label>PDF do certificado *</label><input type="file" name="pdf" id="pdfFile" accept="application/pdf,.pdf" required></div>
          <div class="field"><label>Assinante</label>
            <select name="signer_id" id="signerSel"><option value="0">Sem assinatura</option><?php foreach ($signers as $sg): ?><option value="<?= (int)$sg['id'] ?>"><?= e($sg['name']) ?></option><?php endforeach; ?></select></div>
          <div class="field"><label>Nome da pessoa *</label><input name="nome" required maxlength="180"></div>
          <div class="field"><label>Evento *</label><input name="evento" required maxlength="220"></div>
          <div class="field"><label>Tipo</label><select name="tipo"><?php foreach (cert_types() as $k => $v): ?><option value="<?= e($k) ?>"><?= e($v) ?></option><?php endforeach; ?></select></div>
          <div class="field"><label>Horas</label><input name="horas" type="number" min="1" max="9999"></div>
          <div class="field"><label>Data</label><input name="data" type="date" value="<?= date('Y-m-d') ?>"></div>
          <div class="field"><label>E-mail (opcional)</label><input name="email" type="email" maxlength="190"></div>
        </div>
        <div class="btn-row"><button type="submit" class="btn" id="submitBtn">Registrar e assinar</button></div>
      </div>
    </section>

    <section class="card">
      <div class="card-header"><h3>Posição do selo</h3></div>
      <div class="card-body">
        <div class="pv-wrap" id="pvWrap"><div class="pv-empty" id="pvEmpty">Escolha o PDF ao lado para ver a prévia e posicionar o selo.</div></div>
        <div class="pv-nav" id="pvNav" hidden>
          <button type="button" class="btn-sm outline" id="pgPrev">‹</button>
          <span id="pgLabel">Página 1 de 1</span>
          <button type="button" class="btn-sm outline" id="pgNext">›</button>
        </div>
        <div class="field" style="margin-top:1rem"><label>Tamanho do selo</label>
          <div class="range-row"><input type="range" id="sizeRange" min="14" max="50" step="1" value="22"><span id="sizeLabel" class="hint">22%</span></div></div>
        <label class="hint" style="display:flex;gap:.5rem;align-items:center;margin-top:.75rem"><input type="checkbox" name="all_pages" value="1" id="allPages" style="width:auto"> Aplicar em todas as páginas (mesma posição)</label>
        <p class="hint" style="margin-top:.75rem">Arraste o selo azul até o lugar desejado. O tamanho real no PDF acompanha a proporção da prévia.</p>
      </div>
    </section>
  </div>
</form>
<?php
$js = '<script src="../js/vendor/pdfjs/pdf.min.js"></script><script>window.SIGNERS=' . json_encode($sigMap, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) . ';</script><script src="../js/assinar-pdf.js"></script>';
admin_page_end($js);
