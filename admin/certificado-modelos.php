<?php
/**
 * admin/certificado-modelos.php — Modelos de certificado (arte + campos) e assinantes (imagem de assinatura).
 */
declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
require_once dirname(__DIR__) . '/includes/certificate_issue.php';
require_role('superadmin', 'admin');

$db = getDB();

// Serve imagens (fundo/assinatura) só para o admin logado.
if (isset($_GET['asset'])) {
    $path = cert_asset_path((string)$_GET['asset']);
    if (!$path) { http_response_code(404); exit; }
    header('Content-Type: ' . (str_ends_with($path, '.png') ? 'image/png' : 'image/jpeg'));
    header('Cache-Control: private, max-age=300');
    readfile($path);
    exit;
}

$msg = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'add_signer') {
        $name = trim((string)($_POST['name'] ?? ''));
        $role = trim((string)($_POST['role'] ?? ''));
        if (mb_strlen($name, 'UTF-8') < 3 || mb_strlen($name, 'UTF-8') > 160) {
            $errors[] = 'Informe o nome do assinante (3 a 160 caracteres).';
        } else {
            $file = null;
            if (!empty($_FILES['image']['name'])) {
                [$file, $err] = cert_save_image_upload($_FILES['image'], 'sig', 2 * 1024 * 1024);
                if ($err) $errors[] = $err;
            }
            if (!$errors) {
                $db->prepare('INSERT INTO certificate_signers (name, role, image_file, created_at) VALUES (?, ?, ?, ?)')
                   ->execute([$name, mb_substr($role, 0, 160, 'UTF-8') ?: null, $file, date('Y-m-d H:i:s')]);
                $msg = 'Assinante adicionado.';
            }
        }
    }

    if ($action === 'delete_signer') {
        $id = (int)($_POST['id'] ?? 0);
        $sg = cert_load_signer($db, $id);
        if ($sg) {
            if ($p = cert_asset_path($sg['image_file'])) @unlink($p);
            $db->prepare('UPDATE certificate_templates SET signer1_id = NULL WHERE signer1_id = ?')->execute([$id]);
            $db->prepare('UPDATE certificate_templates SET signer2_id = NULL WHERE signer2_id = ?')->execute([$id]);
            $db->prepare('DELETE FROM certificate_signers WHERE id = ?')->execute([$id]);
            $msg = 'Assinante removido.';
        }
    }

    if ($action === 'new_template') {
        $name = trim((string)($_POST['name'] ?? ''));
        if (mb_strlen($name, 'UTF-8') < 3 || mb_strlen($name, 'UTF-8') > 160) {
            $errors[] = 'Informe o nome do modelo (3 a 160 caracteres).';
        } else {
            $db->prepare('INSERT INTO certificate_templates (name, orientation, body_text, layout_json, created_at) VALUES (?, ?, ?, ?, ?)')
               ->execute([$name, 'L', cert_default_body(), json_encode(cert_default_layout()), date('Y-m-d H:i:s')]);
            header('Location: certificado-modelo.php?id=' . (int)$db->lastInsertId());
            exit;
        }
    }

    if ($action === 'delete_template') {
        $tpl = cert_load_template($db, (int)($_POST['id'] ?? 0));
        if ($tpl) {
            if ($p = cert_asset_path($tpl['background_file'])) @unlink($p);
            $db->prepare('DELETE FROM certificate_templates WHERE id = ?')->execute([$tpl['id']]);
            $msg = 'Modelo excluído (os certificados já emitidos continuam válidos).';
        }
    }
}

$signers   = $db->query('SELECT * FROM certificate_signers ORDER BY name')->fetchAll();
$templates = $db->query('SELECT * FROM certificate_templates ORDER BY name')->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Modelos de certificado — Admin CoBraLT</title>
  <style>
    body{font-family:'Segoe UI',sans-serif;background:#F1F5F9;color:#1E293B;margin:0;padding:2rem;}
    h1{color:#002B4E;font-size:1.3rem;} h2{color:#002B4E;font-size:1rem;margin:0 0 .75rem;} a{color:#0284C7;}
    .card{background:#fff;border:1px solid #E2E8F0;border-radius:12px;padding:1.25rem;margin-bottom:1.25rem;max-width:860px;}
    input,select{box-sizing:border-box;padding:.55rem .7rem;border:1.5px solid #E2E8F0;border-radius:8px;font:inherit;}
    button{padding:.55rem 1rem;border:0;border-radius:8px;background:#002B4E;color:#fff;font:inherit;font-weight:600;cursor:pointer;} button.danger{background:#EF4444;}
    form.row{display:flex;gap:.5rem;flex-wrap:wrap;align-items:center;}
    .ok{background:#D1FAE5;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;max-width:860px;} .err{background:#FEE2E2;border-radius:8px;padding:.75rem 1rem;margin-bottom:1rem;max-width:860px;}
    table{width:100%;border-collapse:collapse;font-size:.9rem;} th,td{text-align:left;padding:.5rem;border-bottom:1px solid #E2E8F0;vertical-align:middle;}
    .sig{height:36px;background:#f8fafc;border:1px solid #E2E8F0;border-radius:4px;}
  </style>
</head>
<body>
  <p><a href="dashboard.php">← Painel</a> · <a href="certificados.php">Certificados</a></p>
  <h1>Modelos e assinaturas</h1>
  <?php if ($msg): ?><div class="ok"><?= e($msg) ?></div><?php endif; ?>
  <?php foreach ($errors as $er): ?><div class="err"><?= e($er) ?></div><?php endforeach; ?>

  <div class="card">
    <h2>Assinantes</h2>
    <p style="margin-top:0">Quem assina os certificados. Envie a assinatura como imagem PNG com fundo transparente (JPG também funciona). Nome e cargo aparecem sob a linha de assinatura.</p>
    <form method="post" enctype="multipart/form-data" class="row">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="action" value="add_signer">
      <input name="name" placeholder="Nome do assinante" required maxlength="160">
      <input name="role" placeholder="Cargo (ex.: Presidente)" maxlength="160">
      <input type="file" name="image" accept="image/png,image/jpeg">
      <button type="submit">Adicionar</button>
    </form>
    <table style="margin-top:1rem">
      <?php foreach ($signers as $sg): ?>
        <tr>
          <td><?php if ($sg['image_file']): ?><img class="sig" src="?asset=<?= e($sg['image_file']) ?>" alt=""><?php endif; ?></td>
          <td><strong><?= e($sg['name']) ?></strong><br><small><?= e((string)$sg['role']) ?></small></td>
          <td><form method="post" onsubmit="return confirm('Remover este assinante?')">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="delete_signer"><input type="hidden" name="id" value="<?= (int)$sg['id'] ?>">
            <button class="danger">Remover</button></form></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$signers): ?><tr><td>Nenhum assinante ainda.</td></tr><?php endif; ?>
    </table>
  </div>

  <div class="card">
    <h2>Modelos</h2>
    <p style="margin-top:0">Um modelo é a arte do certificado (imagem feita no Canva, por exemplo) com o nome, o texto, as assinaturas e o QR posicionados por você.</p>
    <form method="post" class="row">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="action" value="new_template">
      <input name="name" placeholder="Nome do modelo (ex.: CoLT 2026 — participante)" required maxlength="160" style="min-width:300px">
      <button type="submit">Criar modelo</button>
    </form>
    <table style="margin-top:1rem">
      <?php foreach ($templates as $t): ?>
        <tr>
          <td><strong><?= e($t['name']) ?></strong></td>
          <td><a href="certificado-modelo.php?id=<?= (int)$t['id'] ?>">Editar</a> · <a href="certificado-modelo.php?id=<?= (int)$t['id'] ?>&amp;preview=1" target="_blank">Prévia PDF</a></td>
          <td><form method="post" onsubmit="return confirm('Excluir este modelo?')">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action" value="delete_template"><input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
            <button class="danger">Excluir</button></form></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$templates): ?><tr><td>Nenhum modelo ainda. Sem modelo, os certificados usam o layout padrão.</td></tr><?php endif; ?>
    </table>
  </div>
</body>
</html>
