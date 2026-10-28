<?php
/**
 * admin/certificado-assinar-pdf.php — Assina um certificado PDF já pronto:
 * registra no banco e carimba QR, código, link e assinatura. O PDF carimbado é o que vale na verificação.
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
    $corner = in_array($_POST['corner'] ?? '', ['br', 'bl', 'tr', 'tl'], true) ? $_POST['corner'] : 'br';
    $pages  = in_array($_POST['pages'] ?? '', ['first', 'last', 'all'], true) ? $_POST['pages'] : 'first';
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
            [$bytes, $stampErr] = cert_stamp_pdf($f['tmp_name'], $cert, $signer, $corner, $pages);
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
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Assinar PDF pronto — Admin CoBraLT</title>
  <style>
    body{font-family:'Segoe UI',sans-serif;background:#F1F5F9;color:#1E293B;margin:0;padding:2rem;}
    h1{color:#002B4E;font-size:1.3rem;} a{color:#0284C7;}
    .card{background:#fff;border:1px solid #E2E8F0;border-radius:12px;padding:1.25rem;margin-bottom:1.25rem;max-width:760px;}
    .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:.75rem;}
    label{display:block;font-size:.75rem;font-weight:600;color:#475569;text-transform:uppercase;}
    input,select{width:100%;box-sizing:border-box;padding:.55rem .7rem;border:1.5px solid #E2E8F0;border-radius:8px;font:inherit;}
    button{padding:.55rem 1rem;border:0;border-radius:8px;background:#002B4E;color:#fff;font:inherit;font-weight:600;cursor:pointer;}
    .ok{background:#D1FAE5;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;max-width:760px;} .err{background:#FEE2E2;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;max-width:760px;}
  </style>
</head>
<body>
  <p><a href="dashboard.php">← Painel</a> · <a href="certificados.php">Certificados</a> · <a href="certificado-modelos.php">Modelos e assinaturas</a></p>
  <h1>Assinar PDF pronto</h1>

  <?php if ($done): ?>
    <div class="ok">
      <strong>Certificado registrado e assinado:</strong> <?= e($done['code']) ?><br>
      <a href="certificados.php?pdf=<?= (int)$done['id'] ?>">Baixar o PDF assinado</a> ·
      <a href="autenticar.php?c=<?= e(rawurlencode((string)$done['code'])) ?>">Conferir</a><br>
      <small>Entregue <strong>este</strong> arquivo: a verificação por PDF compara com ele, byte a byte.</small>
    </div>
  <?php endif; ?>
  <?php foreach ($errors as $er): ?><div class="err"><?= e($er) ?></div><?php endforeach; ?>

  <div class="card">
    <p style="margin-top:0">Envie o certificado final em PDF (feito no Canva, Word etc.) e informe quem o recebe. O sistema registra no banco, gera o código e carimba um selo com <strong>QR code, código, link de verificação e assinatura</strong> no canto escolhido.</p>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <div class="grid">
        <div><label>PDF do certificado *<input type="file" name="pdf" accept="application/pdf,.pdf" required></label></div>
        <div><label>Nome da pessoa *<input name="nome" required maxlength="180"></label></div>
        <div><label>Evento *<input name="evento" required maxlength="220"></label></div>
        <div><label>Tipo<select name="tipo"><?php foreach (cert_types() as $k => $v): ?><option value="<?= e($k) ?>"><?= e($v) ?></option><?php endforeach; ?></select></label></div>
        <div><label>Horas<input name="horas" type="number" min="1" max="9999"></label></div>
        <div><label>Data<input name="data" type="date" value="<?= date('Y-m-d') ?>"></label></div>
        <div><label>E-mail (opcional)<input name="email" type="email" maxlength="190"></label></div>
        <div><label>Assinante<select name="signer_id"><option value="0">Sem assinatura</option><?php foreach ($signers as $sg): ?><option value="<?= (int)$sg['id'] ?>"><?= e($sg['name']) ?></option><?php endforeach; ?></select></label></div>
        <div><label>Posição do selo<select name="corner">
          <option value="br">Canto inferior direito</option><option value="bl">Canto inferior esquerdo</option>
          <option value="tr">Canto superior direito</option><option value="tl">Canto superior esquerdo</option></select></label></div>
        <div><label>Páginas<select name="pages"><option value="first">Primeira</option><option value="last">Última</option><option value="all">Todas</option></select></label></div>
      </div>
      <p><button type="submit">Registrar e assinar</button></p>
    </form>
  </div>
</body>
</html>
