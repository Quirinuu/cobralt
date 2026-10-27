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
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow">
  <title>Formulários — Admin CoBraLT</title>
  <style>
    body{font-family:'Segoe UI',sans-serif;background:#F1F5F9;color:#1E293B;margin:0;padding:2rem;}
    h1{color:#002B4E;font-size:1.3rem;} a{color:#0284C7;}
    .card{background:#fff;border:1px solid #E2E8F0;border-radius:12px;padding:1rem 1.25rem;margin:0 0 1rem;}
    .meta{font-size:.8rem;color:#64748B;margin-bottom:.5rem;}
    dl{margin:0;display:grid;grid-template-columns:max-content 1fr;gap:.2rem 1rem;}
    dt{font-weight:600;color:#475569;} dd{margin:0;word-break:break-word;}
  </style>
</head>
<body>
  <p><a href="dashboard.php">← Painel</a></p>
  <h1>Formulários recebidos</h1>
  <p>
    Filtrar:
    <a href="formularios.php">todos</a>
    <?php foreach ($types as $t): ?> · <a href="?tipo=<?= e((string)$t) ?>"><?= e((string)$t) ?></a><?php endforeach; ?>
    · <a href="?export=csv<?= $tipo !== '' ? '&amp;tipo=' . e($tipo) : '' ?>">Exportar CSV</a>
  </p>
  <?php if (!$rows): ?><p>Nenhum envio ainda.</p><?php endif; ?>
  <?php foreach ($rows as $r):
    $data = json_decode((string)$r['payload_json'], true);
    $data = is_array($data) ? $data : []; ?>
    <div class="card">
      <div class="meta">#<?= (int)$r['id'] ?> · <?= e((string)$r['type']) ?> · <?= e((string)$r['created_at']) ?> · <?= e((string)$r['ip']) ?></div>
      <dl>
        <?php foreach ($data as $k => $v): ?>
          <dt><?= e((string)$k) ?></dt><dd><?= e((string)$v) ?></dd>
        <?php endforeach; ?>
      </dl>
    </div>
  <?php endforeach; ?>
</body>
</html>
