<?php
// Uso: php tests/certificates_test.php  (usa SQLite em memória)
declare(strict_types=1);
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/certificates.php';
require_once dirname(__DIR__) . '/includes/certificate_pdf.php';

function check(bool $ok, string $what): void {
    echo ($ok ? 'ok   ' : 'FAIL ') . $what . PHP_EOL;
    if (!$ok) { $GLOBALS['failed'] = true; }
}

$db = new PDO('sqlite::memory:', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]);
cert_create_tables($db);

$code = cert_format_code(cert_generate_raw());
check(preg_match('/^CBL(-[A-Z2-9]{4}){3}$/', $code) === 1, 'formato do codigo');
check(cert_normalize_code(strtolower(str_replace('-', ' ', $code))) === $code, 'normaliza minusculas/espacos');
check(cert_normalize_code('CBL-0000-1111-IIII') === '', 'rejeita simbolos fora do alfabeto');
check(cert_normalize_code("' OR 1=1 --") === '', 'rejeita lixo');

[$d, $err] = cert_validate(['nome' => 'Maria Silva', 'evento' => 'XXVIII CoLT', 'tipo' => 'palestrante', 'horas' => '8', 'data' => '15/10/2026']);
check($err === null && $d['issued_at'] === '2026-10-15', 'valida data BR');
check(cert_validate(['nome' => 'Ma', 'evento' => 'X'])[1] !== null, 'rejeita nome curto');
check(cert_validate(['nome' => 'Maria Silva', 'evento' => 'Evento', 'tipo' => 'xyz'])[1] !== null, 'rejeita tipo');

$row = cert_insert($db, $d, 1);
check(cert_find($db, $row['code'])['holder_name'] === 'Maria Silva', 'insere e busca');
cert_set_status($db, (int)$row['id'], true, 'teste');
check(cert_find($db, $row['code'])['status'] === 'revogado', 'revoga');
cert_set_status($db, (int)$row['id'], false);
check(cert_find($db, $row['code'])['status'] === 'valido', 'reativa');

for ($i = 0; $i < 30; $i++) { cert_log_check($db, 'X', false, '1.2.3.4'); }
check(cert_check_rate_limited($db, '1.2.3.4') && !cert_check_rate_limited($db, '9.9.9.9'), 'rate limit por IP');

check(str_starts_with(cert_pdf($row), '%PDF'), 'gera PDF');

require_once dirname(__DIR__) . '/includes/certificate_verify.php';
$pdf = cert_pdf($row);
check($pdf === cert_pdf($row), 'PDF deterministico');
check(cert_extract_code_from_pdf($pdf) === $row['code'], 'extrai codigo do PDF');
$r = cert_verify_pdf($db, $pdf);
check($r['state'] === 'found' && $r['identical'], 'PDF original e identico');
$r = cert_verify_pdf($db, str_replace('MARIA', 'MARTA', $pdf));
check($r['state'] === 'found' && !$r['identical'], 'PDF alterado e detectado');
check(cert_verify_pdf($db, 'not a pdf')['state'] === 'invalid', 'rejeita arquivo que nao e PDF');
check(cert_verify_pdf($db, '%PDF-1.4 sem codigo')['state'] === 'no_code', 'PDF sem codigo');
check(cert_verify_pdf($db, '%PDF-1.4 /Subject (CBL-AAAA-BBBB-CCCC)')['state'] === 'notfound', 'codigo inexistente');

// --- modelos, carimbo em PDF pronto e verificação por hash guardado ---
require_once dirname(__DIR__) . '/includes/certificate_issue.php';
cert_upgrade_tables_v2($db);
$tpl = ['orientation' => 'L', 'background_file' => null, 'body_text' => '{nome} - {evento} - {data}', 'layout_json' => null];
$tplPdf = cert_render_template($row, $tpl, ['sig1' => ['name' => 'Fulano de Tal', 'role' => 'Presidente', 'image_file' => null], 'sig2' => null]);
check(str_starts_with($tplPdf, '%PDF') && cert_extract_code_from_pdf($tplPdf) === $row['code'], 'PDF de modelo traz o codigo');

[$stamped, $serr] = cert_stamp_pdf(dirname(__DIR__) . '/assets/pdfs/programa-party-brasil-2026-ficha.pdf', $row, ['name' => 'Fulano', 'role' => 'Dir', 'image_file' => null], 'br', 'first');
check($stamped !== null && cert_extract_code_from_pdf($stamped) === $row['code'], 'carimba PDF pronto e grava o codigo');
check(cert_stamp_pdf(__FILE__, $row, null)[0] === null, 'recusa arquivo que nao e PDF no carimbo');

$db->prepare('UPDATE certificates SET pdf_sha256 = ? WHERE id = ?')->execute([hash('sha256', $stamped), $row['id']]);
check(cert_verify_pdf($db, $stamped)['identical'] === true, 'verifica por hash guardado');
check(cert_verify_pdf($db, $stamped . ' ')['identical'] === false, 'detecta alteracao em 1 byte');
exit(empty($GLOBALS['failed']) ? 0 : 1);
