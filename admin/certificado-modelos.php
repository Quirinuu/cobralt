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
cert_seed_default_templates($db);

// Serve imagens (fundo/assinatura) só para o admin logado.
if (isset($_GET['asset'])) {
    $path = cert_asset_path((string)$_GET['asset']);
    if (!$path) { http_response_code(404); exit; }
    header('Content-Type: ' . (str_ends_with($path, '.png') ? 'image/png' : (str_ends_with($path, '.pdf') ? 'application/pdf' : 'image/jpeg')));
    header('Cache-Control: private, max-age=300');
    readfile($path);
    exit;
}

// O navegador do admin desenha a 1ª página do PDF de fundo e envia a prévia (JPG) para ficar guardada: abre instantâneo nas próximas vezes.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_preview') {
    csrf_verify();
    $bg = (string)($_POST['background'] ?? '');
    $own = $db->prepare('SELECT COUNT(*) FROM certificate_templates WHERE background_file = ?');
    $own->execute([$bg]);
    $raw = (string)($_POST['image'] ?? '');
    if (!str_ends_with($bg, '.pdf') || !cert_asset_path($bg) || (int)$own->fetchColumn() === 0
        || !preg_match('#^data:image/jpeg;base64,([A-Za-z0-9+/=]+)$#', $raw, $m) || strlen($raw) > 3 * 1024 * 1024) {
        json_fail('Prévia inválida.', 422);
    }
    $img = base64_decode($m[1], true);
    $info = $img !== false ? @getimagesizefromstring($img) : false;
    if (!$info || $info['mime'] !== 'image/jpeg' || $info[0] > 3000 || $info[1] > 3000) {
        json_fail('Prévia inválida.', 422);
    }
    file_put_contents(cert_storage_dir('certificados-arquivos') . '/' . cert_preview_name($bg), $img);
    json_ok();
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
            $bgFile = null;
            $orientation = 'L';
            if (!empty($_FILES['background']['name'])) {
                [$bgFile, $orientation, $err] = cert_save_background($_FILES['background']);
                if ($err) { $errors[] = $err; }
            }
            if (!$errors) {
                $db->prepare('INSERT INTO certificate_templates (name, background_file, orientation, body_text, layout_json, created_at) VALUES (?, ?, ?, ?, ?, ?)')
                   ->execute([$name, $bgFile, $orientation, cert_default_body(), json_encode(cert_default_layout()), date('Y-m-d H:i:s')]);
                header('Location: certificado-modelo.php?id=' . (int)$db->lastInsertId());
                exit;
            }
        }
    }

    if ($action === 'duplicate_template') {
        $src = cert_load_template($db, (int)($_POST['id'] ?? 0));
        if ($src) {
            $bgCopy = null;
            if ($old = cert_asset_path($src['background_file'])) {
                $bgCopy = 'bg_' . bin2hex(random_bytes(10)) . '.' . pathinfo($old, PATHINFO_EXTENSION);
                if (!copy($old, cert_storage_dir('certificados-arquivos') . '/' . $bgCopy)) { $bgCopy = null; }
                elseif ($prev = cert_asset_path(cert_preview_name($src['background_file']))) { @copy($prev, cert_storage_dir('certificados-arquivos') . '/' . cert_preview_name($bgCopy)); }
            }
            $db->prepare('INSERT INTO certificate_templates (name, background_file, orientation, body_text, layout_json, signer1_id, signer2_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)')
               ->execute([mb_substr('Cópia de ' . $src['name'], 0, 160, 'UTF-8'), $bgCopy, $src['orientation'], $src['body_text'], $src['layout_json'], $src['signer1_id'], $src['signer2_id'], date('Y-m-d H:i:s')]);
            header('Location: certificado-modelo.php?id=' . (int)$db->lastInsertId());
            exit;
        }
        $errors[] = 'Modelo não encontrado.';
    }

    if ($action === 'delete_template') {
        $tpl = cert_load_template($db, (int)($_POST['id'] ?? 0));
        if ($tpl) {
            if ($p = cert_asset_path($tpl['background_file'])) @unlink($p);
            if ($tpl['background_file'] && ($p = cert_asset_path(cert_preview_name($tpl['background_file'])))) @unlink($p);
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
      <p class="hint" style="margin-bottom:1rem">Um modelo é o certificado já pronto (PDF ou imagem, feito no Canva por exemplo) com o <strong>nome</strong>, o <strong>texto</strong> e o <strong>QR</strong> posicionados por você. As assinaturas e o desenho que já estão no PDF/imagem são mantidos como estão. Sem modelo, os certificados usam o layout padrão.</p>
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action" value="new_template">
        <div class="grid-form">
          <div class="field"><label>Nome do modelo *</label><input type="text" name="name" placeholder="Ex.: LAEFT — participante" required maxlength="160"></div>
          <div class="field"><label>Certificado pronto (PDF, JPG ou PNG)</label><input type="file" name="background" accept="application/pdf,image/png,image/jpeg,.pdf"></div>
        </div>
        <div class="btn-row"><button type="submit" class="btn">Criar modelo</button></div>
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
              <form method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="duplicate_template"><input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                <button class="btn-sm outline" title="Cria uma cópia para o próximo evento (mesmo fundo, texto e posições)">Duplicar</button></form>
              <form method="post" onsubmit="return confirm('Excluir este modelo?')">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="delete_template"><input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
                <button class="btn-sm danger">Excluir</button></form>
            </div></td>
          </tr>
        <?php endforeach; ?>
        <?php if (!$templates): ?><tr><td class="empty">Nenhum modelo ainda. Crie o primeiro enviando o seu certificado pronto (PDF).</td></tr><?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
<?php
admin_page_end();
