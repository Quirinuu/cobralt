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

[$stamped, $serr] = cert_stamp_pdf(dirname(__DIR__) . '/assets/pdfs/programa-party-brasil-2026-ficha.pdf', $row, ['name' => 'Fulano', 'role' => 'Dir', 'image_file' => null], ['page' => 1, 'x' => 70, 'y' => 85, 'w' => 22]);
check($stamped !== null && cert_extract_code_from_pdf($stamped) === $row['code'], 'carimba PDF pronto e grava o codigo');
check(cert_stamp_pdf(dirname(__DIR__) . '/assets/pdfs/programa-party-brasil-2026-ficha.pdf', $row, null, ['page' => 99])[0] === null, 'recusa pagina inexistente');
check(cert_stamp_pdf(__FILE__, $row, null, ['page' => 1])[0] === null, 'recusa arquivo que nao e PDF no carimbo');

$db->prepare('UPDATE certificates SET pdf_sha256 = ? WHERE id = ?')->execute([hash('sha256', $stamped), $row['id']]);
check(cert_verify_pdf($db, $stamped)['identical'] === true, 'verifica por hash guardado');
check(cert_verify_pdf($db, $stamped . ' ')['identical'] === false, 'detecta alteracao em 1 byte');

// --- busca por nome/e-mail ---
$db->prepare('UPDATE certificates SET holder_email = ? WHERE id = ?')->execute(['maria@exemplo.com', $row['id']]);
check(count(cert_search($db, 'Maria Silva')) === 1, 'busca publica por nome');
check(count(cert_search($db, 'maria@exemplo.com')) === 1 && count(cert_search($db, 'MARIA@EXEMPLO.COM')) === 1, 'busca publica por e-mail completo (sem diferenciar maiusculas)');
check(cert_search($db, 'maria@exemplo') === [], 'busca publica NAO aceita e-mail parcial');
check(count(cert_search($db, 'exemplo.com', true)) === 1, 'busca admin aceita parte do e-mail');
check(!cert_public_query_ok('%%%%%%') && !cert_public_query_ok('Silva') && cert_public_query_ok('Maria Silva') && cert_public_query_ok('a@b.co'), 'regras minimas da busca publica');

// --- caixas de texto livres e campos novos ---
$box = cert_clean_text_box(['text' => str_repeat('x', 500), 'x' => 999, 'y' => -5, 'w' => 'abc', 'size' => 9999, 'color' => 'red', 'align' => 'X', 'font' => 'Comic', 'style' => 'Z']);
check(mb_strlen($box['text']) === 300 && $box['x'] == 100 && $box['y'] == 0 && $box['size'] == 80 && $box['color'] === '#3C3C3C' && $box['align'] === 'L' && $box['font'] === 'Times' && $box['style'] === '', 'caixa livre: valores invalidos sao saneados');
$tplX = ['orientation' => 'L', 'background_file' => null, 'body_text' => null, 'layout_json' => json_encode(['extra1' => ['text' => 'Data: {data} | Horas: {horas} | {codigo}', 'x' => 10, 'y' => 60, 'w' => 40, 'size' => 12]])];
$pdfX = cert_render_template($row, $tplX, ['sig1' => null, 'sig2' => null]);
check(str_contains($pdfX, 'Data: 15/10/2026') && str_contains($pdfX, 'Horas: 8') && str_contains($pdfX, $row['code']), 'caixa livre renderiza {data}, {horas} e {codigo}');

// --- planilhas: XLSX com prefixo de namespace (x:) e horas como decimal ---
require_once dirname(__DIR__) . '/includes/certificate_import.php';
$xl = sys_get_temp_dir() . '/cert-test-' . bin2hex(random_bytes(4)) . '.xlsx';
$zip = new ZipArchive();
$zip->open($xl, ZipArchive::CREATE);
$ns = 'xmlns:x="http://schemas.openxmlformats.org/spreadsheetml/2006/main"';
$zip->addFromString('xl/sharedStrings.xml', "<?xml version=\"1.0\"?><x:sst $ns><x:si><x:t>nome</x:t></x:si><x:si><x:t>evento</x:t></x:si><x:si><x:t>horas</x:t></x:si><x:si><x:t>data</x:t></x:si><x:si><x:t>Ana Souza</x:t></x:si></x:sst>");
$zip->addFromString('xl/worksheets/sheet1.xml', "<?xml version=\"1.0\"?><x:worksheet $ns><x:sheetData>"
    . '<x:row r="1"><x:c r="A1" t="s"><x:v>0</x:v></x:c><x:c r="B1" t="s"><x:v>1</x:v></x:c><x:c r="C1" t="s"><x:v>2</x:v></x:c><x:c r="D1" t="s"><x:v>3</x:v></x:c></x:row>'
    . '<x:row r="2"><x:c r="A2" t="s"><x:v>4</x:v></x:c><x:c r="B2" t="str"><x:v>Curso X</x:v></x:c><x:c r="C2"><x:v>5.0</x:v></x:c><x:c r="D2"><x:v>46303</x:v></x:c></x:row>'
    . '</x:sheetData></x:worksheet>');
$zip->close();
[$xr, $xe] = cert_table_from_upload(['tmp_name' => $xl, 'name' => 'p.xlsx', 'size' => filesize($xl), 'error' => 0]);
@unlink($xl);
check($xe === null && count($xr) === 1 && $xr[0]['nome'] === 'Ana Souza' && $xr[0]['evento'] === 'Curso X', 'le XLSX com prefixo x: e celulas str');
[$xd, $xerr] = cert_validate(cert_normalize_row($xr[0]));
check($xerr === null && $xd['hours'] === 5 && $xd['issued_at'] === '2026-10-08', 'horas "5.0" e data serial do Excel viram valores validos');
check(cert_validate(cert_normalize_row(['nome' => 'Ana Souza', 'evento' => 'Curso X', 'horas' => '7.5']))[1] !== null, 'horas fracionadas continuam recusadas');

// --- texto sempre começa com maiúscula ---
check(str_starts_with(cert_body_text($row, '{tipo_frase} {evento}'), 'Participou do evento') || str_starts_with(cert_body_text($row, '{tipo_frase} {evento}'), 'Atuou'), 'texto comeca com letra maiuscula');
check(cert_body_text($row, 'ótimo {nome}') === 'Ótimo Maria Silva', 'maiuscula tambem com acento');

// --- historico de acoes e desfazer ---
cert_upgrade_tables_v3($db);
$cnt = static fn() => (int)$db->query('SELECT COUNT(*) FROM certificates')->fetchColumn();
$before = $cnt();
$a = [];
for ($i = 0; $i < 3; $i++) { $a[] = (int)cert_insert($db, ['holder_name' => "Lote $i Teste", 'holder_email' => null, 'event_name' => 'Evento', 'cert_type' => 'participante', 'hours' => null, 'issued_at' => '2026-10-15'], 1)['id']; }
$act = cert_log_action($db, 'lote', 'Importação de teste', $a, 1);
$single = (int)cert_insert($db, ['holder_name' => 'Avulso Teste', 'holder_email' => null, 'event_name' => 'Evento', 'cert_type' => 'participante', 'hours' => null, 'issued_at' => '2026-10-15'], 1)['id'];
$act2 = cert_log_action($db, 'emissao', 'Avulso', [$single], 1);
check($cnt() === $before + 4, 'acoes criam certificados ligados ao historico');
check(cert_undo_action($db, $act) === 3 && $cnt() === $before + 1, 'desfazer apaga so os certificados daquela acao');
check(cert_undo_action($db, $act) === null, 'desfazer duas vezes nao faz nada');
check(cert_undo_action($db, 99999) === null, 'acao inexistente');
check(cert_undo_action($db, $act2) === 1 && $cnt() === $before, 'desfazer acao avulsa');
check(cert_undo_legacy($db) === $before && $cnt() === 0, 'apagar anteriores ao historico remove so os sem acao');
exit(empty($GLOBALS['failed']) ? 0 : 1);
