<?php
/**
 * admin/formularios.php - Lista contato, filiação e inscrições recebidos pelo site.
 * ?export=csv baixa tudo (respeita o filtro de tipo).
 */
declare(strict_types=1);

require_once __DIR__ . '/_auth.php';
require_role('superadmin', 'admin');

$db   = getDB();
$tipo = preg_replace('/[^a-z0-9_\-]/i', '', (string)($_GET['tipo'] ?? ''));

$sql    = 'SELECT id, type, payload_json, ip, created_at FROM form_submissions' . ($tipo !== '' ? ' WHERE type = ?' : '') . ' ORDER BY id DESC LIMIT 500';
$stmt   = $db->prepare($sql);
$stmt->execute($tipo !== '' ? [$tipo] : []);
$rows   = $stmt->fetchAll();
$types  = $db->query('SELECT DISTINCT type FROM form_submissions ORDER BY type')->fetchAll(PDO::FETCH_COLUMN);

if (($_GET['export'] ?? '') === 'csv') {
    // Prefixo ' evita injeção de fórmula ao abrir no Excel.
    $safe = static fn(string $v): string => preg_match('/^[=+\-@\t\r]/', $v) ? "'" . $v : $v;
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="formularios.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF");
    fputcsv($out, ['id', 'tipo', 'data', 'ip', 'dados'], ',', '"', '');
    foreach ($rows as $r) {
        fputcsv($out, [$r['id'], $safe($r['type']), $r['created_at'], $r['ip'], $safe((string)$r['payload_json'])], ',', '"', '');
    }
    exit;
}
admin_page_start('Formulários', 'formularios', 'Contato, filiação e inscrições recebidos pelo site',
    '<a class="btn-sm outline" href="?export=csv' . ($tipo !== '' ? '&amp;tipo=' . e($tipo) : '') . '">Exportar CSV</a>');
?>
<div class="btn-row" style="margin:0 0 1.25rem">
  <a class="btn-sm <?= $tipo === '' ? '' : 'outline' ?>" href="formularios.php">Todos</a>
  <?php foreach ($types as $t): ?><a class="btn-sm <?= $tipo === $t ? '' : 'outline' ?>" href="?tipo=<?= e(rawurlencode((string)$t)) ?>"><?= e((string)$t) ?></a><?php endforeach; ?>
</div>
<?php if (!$rows): ?><div class="card"><p class="empty">Nenhum envio ainda.</p></div><?php endif; ?>
<?php foreach ($rows as $r):
  $data = json_decode((string)$r['payload_json'], true);
  $data = is_array($data) ? $data : []; ?>
  <section class="card">
    <div class="card-header"><h3><?= e((string)$r['type']) ?> <span class="hint">#<?= (int)$r['id'] ?></span></h3><span class="hint"><?= e((string)$r['created_at']) ?> · <?= e((string)$r['ip']) ?></span></div>
    <div class="card-body">
      <dl style="display:grid;grid-template-columns:max-content 1fr;gap:.35rem 1.25rem;margin:0">
        <?php foreach ($data as $k => $v): ?><dt style="font-weight:700;color:var(--slate-600)"><?= e((string)$k) ?></dt><dd style="margin:0;word-break:break-word"><?= e((string)$v) ?></dd><?php endforeach; ?>
      </dl>
    </div>
  </section>
<?php endforeach; ?>
<?php
admin_page_end();
