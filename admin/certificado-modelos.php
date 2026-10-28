<?php
/**
 * admin/certificado-modelos.php — Modelos de certificado (arte + campos) e assinantes (imagem de assinatura).
 */
declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
require_once dirname(__DIR__) . '/includes/certificate_issue.php';
require_role('superadmin', 'admin');

$db = getDB();
cert_ensure_schema($db);

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
admin_page_start('Modelos e assinaturas', 'modelos', 'A arte dos certificados e quem assina');
admin_alerts($msg, $errors);
?>
<div class="grid-2">
  <section class="card">
    <div class="card-header"><h3>Assinantes</h3></div>
    <div class="card-body">
      <p class="hint" style="margin-bottom:1rem">Quem assina os certificados. Envie a assinatura como imagem PNG com fundo transparente (JPG também funciona). Nome e cargo aparecem sob a linha de assinatura.</p>
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="add_signer">
        <div class="grid-form">
          <div class="field"><label>Nome *</label><input name="name" required maxlength="160"></div>
          <div class="field"><label>Cargo</label><input name="role" maxlength="160" placeholder="Ex.: Presidente"></div>
          <div class="field"><label>Imagem da assinatura</label><input type="file" name="image" accept="image/png,image/jpeg"></div>
        </div>
        <div class="btn-row"><button type="submit" class="btn">Adicionar assinante</button></div>
      </form>
    </div>
    <div class="table-wrap">
      <table>
        <tbody>
        <?php foreach ($signers as $sg): ?>
          <tr>
            <td style="width:110px"><?php if ($sg['image_file']): ?><img class="thumb" src="?asset=<?= e($sg['image_file']) ?>" alt=""><?php endif; ?></td>
            <td><strong><?= e($sg['name']) ?></strong><br><small class="hint"><?= e((string)$sg['role']) ?></small></td>
            <td style="text-align:right"><form method="post" onsubmit="return confirm('Remover este assinante?')">
              <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="action" value="delete_signer"><input type="hidden" name="id" value="<?= (int)$sg['id'] ?>">
              <button class="btn-sm danger">Remover</button></form></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$signers): ?><tr><td class="empty">Nenhum assinante ainda.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <section class="card">
    <div class="card-header"><h3>Modelos de certificado</h3></div>
    <div class="card-body">
      <p class="hint" style="margin-bottom:1rem">Um modelo é a arte do certificado (imagem feita no Canva, por exemplo) com o nome, o texto, as assinaturas e o QR posicionados por você. Sem modelo, os certificados usam o layout padrão.</p>
      <form method="post" class="inline-form">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="new_template">
        <input type="text" name="name" placeholder="Nome do modelo (ex.: CoLT 2026 — participante)" required maxlength="160">
        <button type="submit" class="btn">Criar modelo</button>
      </form>
    </div>
    <div class="table-wrap">
      <table>
        <tbody>
        <?php foreach ($templates as $t): ?>
          <tr>
            <td><strong><?= e($t['name']) ?></strong></td>
            <td><div class="actions" style="justify-content:flex-end">
              <a class="btn-sm" href="certificado-modelo.php?id=<?= (int)$t['id'] ?>">Editar</a>
              <a class="btn-sm outline" href="certificado-modelo.php?id=<?= (int)$t['id'] ?>&amp;preview=1" target="_blank">Prévia PDF</a>
              <form method="post" onsubmit="return confirm('Excluir este modelo?')">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="delete_template"><input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                <button class="btn-sm danger">Excluir</button></form>
            </div></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$templates): ?><tr><td class="empty">Nenhum modelo ainda.</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
<?php
admin_page_end();
