<?php
/**
 * includes/certificate_import.php
 * Leitura de planilhas (CSV ou XLSX) para emissão de certificados em lote.
 * Sem dependências: o XLSX é lido direto do zip (ZipArchive) — primeira aba, valores como estão.
 */

declare(strict_types=1);

const CERT_IMPORT_MAX_ROWS = 500;
const CERT_IMPORT_MAX_BYTES = 2 * 1024 * 1024;

/** @return array{0:?array,1:?string} [linhas associativas (cabeçalho em minúsculas), erro] */
function cert_table_from_upload(array $file): array {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || $file['size'] > CERT_IMPORT_MAX_BYTES) {
        return [null, 'Envie uma planilha CSV ou XLSX de até 2 MB.'];
    }
    $path = (string)$file['tmp_name'];
    $isXlsx = strtolower(pathinfo((string)$file['name'], PATHINFO_EXTENSION)) === 'xlsx'
        || str_starts_with((string)file_get_contents($path, false, null, 0, 2), 'PK');
    [$grid, $err] = $isXlsx ? cert_read_xlsx($path) : cert_read_csv($path);
    if ($err) {
        return [null, $err];
    }
    return cert_grid_to_rows($grid);
}

/** Primeira linha = cabeçalho. Exige nome e evento; ignora linhas totalmente vazias. */
function cert_grid_to_rows(array $grid): array {
    if (!$grid) {
        return [null, 'A planilha está vazia.'];
    }
    $header = array_map(static fn($h) => strtolower(trim((string)preg_replace('/^\xEF\xBB\xBF/', '', (string)$h))), array_shift($grid));
    if (!in_array('nome', $header, true) || !in_array('evento', $header, true)) {
        return [null, 'A primeira linha precisa ter ao menos as colunas: nome, evento (opcionais: tipo, horas, data, email).'];
    }
    $rows = [];
    foreach ($grid as $i => $cols) {
        if (!array_filter($cols, static fn($v) => trim((string)$v) !== '')) {
            continue;
        }
        if (count($rows) >= CERT_IMPORT_MAX_ROWS) {
            return [null, 'Limite de ' . CERT_IMPORT_MAX_ROWS . ' linhas por importação.'];
        }
        $assoc = array_combine($header, array_pad(array_slice($cols, 0, count($header)), count($header), ''));
        $assoc['_linha'] = $i + 2; // número da linha na planilha (cabeçalho é a 1)
        $rows[] = $assoc;
    }
    return $rows ? [$rows, null] : [null, 'Nenhuma linha de dados encontrada na planilha.'];
}

function cert_read_csv(string $path): array {
    $fh = fopen($path, 'r');
    if (!$fh) {
        return [null, 'Não foi possível abrir o arquivo.'];
    }
    $first = (string)fgets($fh);
    rewind($fh);
    $sep = substr_count($first, ';') > substr_count($first, ',') ? ';' : ',';
    $grid = [];
    while (($cols = fgetcsv($fh, 0, $sep, '"', '')) !== false) {
        if ($cols === [null]) {
            continue;
        }
        $grid[] = array_map(static fn($v) => (string)$v, $cols);
    }
    fclose($fh);
    return [$grid, null];
}

function cert_xlsx_col_index(string $ref): int {
    preg_match('/^([A-Z]+)/', $ref, $m);
    $n = 0;
    foreach (str_split($m[1] ?? 'A') as $ch) {
        $n = $n * 26 + (ord($ch) - 64);
    }
    return $n - 1;
}

/** Planilhas de alguns programas gravam o XML com prefixo (<x:sheetData>); remove prefixos/namespaces para ler igual. */
function cert_xml_plain(string $xml): string {
    $xml = preg_replace('/\sxmlns(:[A-Za-z0-9_]+)?="[^"]*"/', '', $xml) ?? $xml;
    $xml = preg_replace('/<(\/?)[A-Za-z0-9_]+:/', '<$1', $xml) ?? $xml;
    return preg_replace('/\s[A-Za-z0-9_]+:([A-Za-z0-9_]+)=/', ' $1=', $xml) ?? $xml;
}

function cert_read_xlsx(string $path): array {
    if (!class_exists('ZipArchive')) {
        return [null, 'Este servidor não lê XLSX. Salve a planilha como CSV e envie de novo.'];
    }
    $zip = new ZipArchive();
    if ($zip->open($path) !== true) {
        return [null, 'Não foi possível abrir o XLSX.'];
    }
    $read = static function (string $name) use ($zip): ?string {
        $st = $zip->statName($name);
        if (!$st || $st['size'] > 20 * 1024 * 1024) {   // evita zip bomba
            return null;
        }
        $data = $zip->getFromName($name);
        return $data === false ? null : $data;
    };

    $shared = [];
    if (($xml = $read('xl/sharedStrings.xml')) !== null) {
        $sx = @simplexml_load_string(cert_xml_plain($xml), 'SimpleXMLElement', LIBXML_NONET);
        foreach ($sx ? $sx->si : [] as $si) {
            $text = '';
            if (isset($si->t)) {
                $text = (string)$si->t;
            } else {
                foreach ($si->r as $r) { $text .= (string)$r->t; }
            }
            $shared[] = $text;
        }
    }

    $sheetXml = null;
    for ($i = 1; $i <= 20 && $sheetXml === null; $i++) {
        $sheetXml = $read('xl/worksheets/sheet' . $i . '.xml');
    }
    $zip->close();
    if ($sheetXml === null) {
        return [null, 'Não encontrei dados na primeira aba do XLSX.'];
    }
    $sx = @simplexml_load_string(cert_xml_plain($sheetXml), 'SimpleXMLElement', LIBXML_NONET);
    if (!$sx || !isset($sx->sheetData)) {
        return [null, 'XLSX inválido.'];
    }

    $grid = [];
    foreach ($sx->sheetData->row as $row) {
        $line = [];
        foreach ($row->c as $c) {
            $idx = cert_xlsx_col_index((string)$c['r']);
            $t = (string)$c['t'];
            if ($t === 's') {
                $val = $shared[(int)$c->v] ?? '';
            } elseif ($t === 'inlineStr') {
                $val = (string)$c->is->t;
            } else {
                $val = (string)$c->v;
            }
            $line[$idx] = $val;
        }
        if ($line) {
            $max = max(array_keys($line));
            $full = [];
            for ($k = 0; $k <= $max; $k++) { $full[] = $line[$k] ?? ''; }
            $grid[] = $full;
        }
    }
    return [$grid, null];
}

/** Datas do Excel chegam como número de série (dias desde 1899-12-30); converte para AAAA-MM-DD. */
function cert_normalize_row(array $row): array {
    $d = trim((string)($row['data'] ?? ''));
    if ($d !== '' && preg_match('/^\d{5}(\.\d+)?$/', $d) && (float)$d > 20000 && (float)$d < 80000) {
        $row['data'] = gmdate('Y-m-d', (int)(((float)$d - 25569) * 86400));
    }
    // Excel guarda números como decimais (5 vira "5.0" ou "5,0"): aceita se for um número inteiro.
    $h = str_replace(',', '.', trim((string)($row['horas'] ?? '')));
    if ($h !== '' && is_numeric($h) && (float)$h == floor((float)$h)) {
        $row['horas'] = (string)(int)$h;
    }
    return $row;
}
