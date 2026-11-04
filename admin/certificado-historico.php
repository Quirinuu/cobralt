<?php
/**
 * admin/certificado-historico.php — Histórico das emissões, com botão para desfazer uma ação inteira
 * (por exemplo, uma importação de 80 certificados feita errado).
 */
declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
require_once dirname(__DIR__) . '/includes/certificate_issue.php';
require_role('superadmin', 'admin');

$db = getDB();
cert_ensure_schema($db);
$msg = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $action = (string)($_POST['action'] ?? '');
    if ($action === 'undo') {
        $n = cert_undo_action($db, (int)($_POST['id'] ?? 0));
        if ($n === null) { $errors[] = 'Esta ação não existe ou já foi desfeita.'; }
        else { $msg = $n . ' certificado(s) apagado(s) — ação desfeita. O backup ficou guardado: use Restaurar se foi engano.'; }
    } elseif ($action === 'undo_legacy') {
        $msg = cert_undo_legacy($db, $adminId) . ' certificado(s) anteriores ao histórico apagado(s). O backup ficou guardado: dá para restaurar abaixo.';
    } elseif ($action === 'restore') {
        $n = cert_restore_action($db, (int)($_POST['id'] ?? 0));
        if ($n === null) { $errors[] = 'Não há backup para restaurar esta ação.'; }
        else { $msg = $n . ' certificado(s) restaurado(s).'; }
    }
}

$legacy = (int)$db->query('SELECT COUNT(*) FROM certificates WHERE action_id IS NULL')->fetchColumn();
$rows = $db->query(
    'SELECT a.*, u.username,
            (SELECT COUNT(*) FROM certificates c WHERE c.action_id = a.id) AS alive
     FROM certificate_actions a LEFT JOIN admin_users u ON u.id = a.created_by
     ORDER BY a.id DESC LIMIT 200'
)->fetchAll();
$kinds = ['emissao' => 'Avulso', 'lote' => 'Planilha', 'pdf' => 'PDF assinado', 'legado' => 'Antigos'];

admin_page_start('Histórico', 'historico', 'Veja o que foi emitido e desfaça uma ação inteira');
admin_alerts($msg, $errors);
?>
<?php if ($legacy > 0): ?>
<section class="card">
  <div class="card-header"><h3>Certificados anteriores ao histórico</h3></div>
  <div class="card-body">
    <p class="hint" style="margin-bottom:1rem">Há <strong><?= $legacy ?></strong> certificado(s) emitidos antes de existir o histórico. Você pode apagar todos de uma vez; fica um <strong>backup</strong> para restaurar depois.</p>
    <form method="post" onsubmit="return confirm('Apagar os <?= $legacy ?> certificados anteriores ao histórico? Fica um backup para restaurar.')">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="action" value="undo_legacy">
      <button type="submit" class="btn danger">Apagar esses <?= $legacy ?> certificados</button>
    </form>
  </div>
</section>
<?php endif; ?>

<section class="card">
  <div class="card-header"><h3>Ações registradas</h3></div>
  <div class="table-wrap">
    <table>
      <thead><tr><th>Quando</th><th>Quem</th><th>O que foi feito</th><th>Certificados</th><th>Situação</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($rows as $r): $undone = (bool)$r['undone_at']; ?>
        <tr>
          <td><?= e(date('d/m/Y H:i', (int)strtotime((string)$r['created_at']))) ?></td>
          <td><?= e((string)($r['username'] ?? '—')) ?></td>
          <td><span class="badge warn"><?= e($kinds[$r['kind']] ?? $r['kind']) ?></span> <?= e((string)$r['summary']) ?></td>
          <td><?= $undone ? '<s>' . (int)$r['undone_count'] . '</s>' : (int)$r['alive'] ?></td>
          <td><?= $undone ? '<span class="badge err">Desfeita em ' . e(date('d/m H:i', (int)strtotime((string)$r['undone_at']))) . '</span>' : '<span class="badge ok">Ativa</span>' ?></td>
          <td>
            <?php if ($undone && cert_has_backup((int)$r['id'])): ?>
            <form method="post" onsubmit="return confirm('Restaurar os certificados desta ação a partir do backup?')">
              <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="action" value="restore"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <button type="submit" class="btn-sm">Restaurar</button>
            </form>
            <?php endif; ?>
            <?php if (!$undone): ?>
            <form method="post" onsubmit="return confirm('Desfazer esta ação? Os <?= (int)$r['alive'] ?> certificado(s) criados por ela serão apagados (fica um backup para restaurar).')">
              <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="action" value="undo"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <button type="submit" class="btn-sm danger">Desfazer</button>
            </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$rows): ?><tr><td colspan="6" class="empty">Nenhuma ação registrada ainda. Cada emissão a partir de agora aparece aqui.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</section>
<?php
admin_page_end();
