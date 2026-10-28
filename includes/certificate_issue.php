<?php
/**
 * includes/certificate_issue.php
 * Emissão completa: gera o PDF (layout padrão, modelo com arte de fundo, ou carimbo em PDF pronto),
 * guarda o arquivo em storage/certificados/ e grava o SHA-256 para a verificação por PDF.
 */

declare(strict_types=1);

require_once __DIR__ . '/certificate_pdf.php';
require_once dirname(__DIR__) . '/vendor/fpdi/autoload.php';

function cert_storage_dir(string $sub = 'certificados'): string {
    $dir = dirname(__DIR__) . '/storage/' . $sub;
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        file_put_contents($dir . '/.htaccess', "Require all denied\n<IfModule !mod_authz_core.c>\n    Order Allow,Deny\n    Deny from all\n</IfModule>\n");
    }
    return $dir;
}

/** Salva uma imagem enviada (JPG/PNG) em storage/certificados-arquivos e devolve o nome do arquivo. */
function cert_save_image_upload(array $file, string $prefix, int $maxBytes = 5 * 1024 * 1024): array {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) return [null, 'Não foi possível receber a imagem.'];
    if ($file['size'] > $maxBytes) return [null, 'A imagem excede ' . ($maxBytes / 1048576) . ' MB.'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
    $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png'][$mime] ?? null;
    if (!$ext || !@getimagesize($file['tmp_name'])) return [null, 'Use uma imagem JPG ou PNG.'];
    $name = $prefix . '_' . bin2hex(random_bytes(10)) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], cert_storage_dir('certificados-arquivos') . '/' . $name)) {
        return [null, 'Erro ao salvar a imagem.'];
    }
    return [$name, null];
}

function cert_asset_path(?string $file): ?string {
    if (!$file || !preg_match('/^[a-z0-9_]+\.(jpg|png)$/', $file)) return null;
    $p = cert_storage_dir('certificados-arquivos') . '/' . $file;
    return is_file($p) ? $p : null;
}

/** Layout padrão de um modelo (posições em % da página; x/w sobre a largura, y sobre a altura). */
function cert_default_layout(): array {
    return [
        'name' => ['x' => 10, 'y' => 40, 'w' => 80, 'size' => 30, 'color' => '#002B4E', 'align' => 'C'],
        'body' => ['x' => 15, 'y' => 54, 'w' => 70, 'size' => 14, 'color' => '#3C3C3C', 'align' => 'C'],
        'sig1' => ['x' => 14, 'y' => 70, 'w' => 28],
        'sig2' => ['x' => 58, 'y' => 70, 'w' => 28],
        'qr'   => ['x' => 84, 'y' => 78, 'w' => 11],
        'code' => ['x' => 6, 'y' => 90, 'w' => 70, 'size' => 9, 'color' => '#3C3C3C', 'align' => 'L'],
    ];
}

function cert_default_body(): string {
    return '{tipo_frase} {evento}{horas_frase}.';
}

function cert_hex_rgb(string $hex): array {
    return preg_match('/^#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i', $hex, $m)
        ? [hexdec($m[1]), hexdec($m[2]), hexdec($m[3])] : [0, 0, 0];
}

function cert_body_text(array $c, string $tpl): string {
    $verbs = [
        'participante' => 'participou do evento',
        'palestrante'  => 'atuou como palestrante no evento',
        'organizador'  => 'atuou na organização do evento',
        'monitor'      => 'atuou como monitor no evento',
    ];
    $date = DateTime::createFromFormat('Y-m-d', (string)$c['issued_at']);
    $h = (int)($c['hours'] ?? 0);
    return strtr($tpl, [
        '{nome}' => (string)$c['holder_name'],
        '{evento}' => (string)$c['event_name'],
        '{tipo}' => cert_types()[$c['cert_type']] ?? (string)$c['cert_type'],
        '{tipo_frase}' => $verbs[$c['cert_type']] ?? 'participou do evento',
        '{horas}' => $h ? (string)$h : '',
        '{horas_frase}' => $h ? ', com carga horária de ' . $h . ' hora' . ($h > 1 ? 's' : '') : '',
        '{data}' => $date ? $date->format('d/m/Y') : (string)$c['issued_at'],
    ]);
}

/** Desenha QR (retângulos) em (x,y) mm com lado $box mm. */
function cert_draw_qr(FPDF $pdf, string $url, float $x, float $y, float $box): void {
    $qr = QRCode::getMinimumQRCode($url, QR_ERROR_CORRECT_LEVEL_M);
    $n = $qr->getModuleCount();
    $cell = $box / ($n + 2);
    $pdf->SetFillColor(255, 255, 255);
    $pdf->Rect($x, $y, $box, $box, 'F');
    $pdf->SetFillColor(0, 0, 0);
    for ($r = 0; $r < $n; $r++) {
        for ($k = 0; $k < $n; $k++) {
            if ($qr->isDark($r, $k)) {
                $pdf->Rect($x + ($k + 1) * $cell, $y + ($r + 1) * $cell, $cell, $cell, 'F');
            }
        }
    }
}

/** Assinatura (imagem + nome + cargo) centralizada em $w mm a partir de ($x,$y). */
function cert_draw_signer(FPDF $pdf, ?array $signer, float $x, float $y, float $w, float $nameSize = 10): void {
    if (!$signer) return;
    $img = cert_asset_path($signer['image_file'] ?? null);
    if ($img) {
        try { $pdf->Image($img, $x + $w * 0.2, $y, $w * 0.6); } catch (Throwable $e) { /* sem assinatura visual */ }
    }
    $lineY = $y + $w * 0.28;
    $pdf->SetDrawColor(80, 80, 80);
    $pdf->SetLineWidth(0.2);
    $pdf->Line($x, $lineY, $x + $w, $lineY);
    $pdf->SetTextColor(40, 40, 40);
    $pdf->SetFont('Helvetica', 'B', $nameSize);
    $pdf->SetXY($x, $lineY + 1);
    $pdf->Cell($w, 5, cert_pdf_text((string)$signer['name']), 0, 2, 'C');
    if (!empty($signer['role'])) {
        $pdf->SetFont('Helvetica', '', $nameSize - 2);
        $pdf->Cell($w, 4, cert_pdf_text((string)$signer['role']), 0, 0, 'C');
    }
}

/** PDF a partir de um modelo (arte de fundo + campos posicionados). */
function cert_render_template(array $c, array $tpl, array $signers): string {
    $landscape = ($tpl['orientation'] ?? 'L') !== 'P';
    [$W, $H] = $landscape ? [297.0, 210.0] : [210.0, 297.0];
    $layout = array_replace_recursive(cert_default_layout(), json_decode((string)($tpl['layout_json'] ?? ''), true) ?: []);

    $pdf = new CertPdf((string)$c['issued_at']);
    $pdf->SetCompression(false);
    $pdf->SetTitle('Certificado ' . $c['code']);
    $pdf->SetSubject((string)$c['code']);
    $pdf->SetCreator('CoBraLT');
    $pdf->SetAutoPageBreak(false);
    $pdf->AddPage($landscape ? 'L' : 'P', 'A4');

    $bg = cert_asset_path($tpl['background_file'] ?? null);
    if ($bg) {
        $pdf->Image($bg, 0, 0, $W, $H);
    }

    // Nome (reduz a fonte até caber em uma linha)
    $n = $layout['name'];
    $nameW = $W * $n['w'] / 100;
    $size = (float)$n['size'];
    $name = cert_pdf_text(mb_strtoupper((string)$c['holder_name'], 'UTF-8'));
    $pdf->SetFont('Helvetica', 'B', $size);
    while ($size > 10 && $pdf->GetStringWidth($name) > $nameW) {
        $size--;
        $pdf->SetFont('Helvetica', 'B', $size);
    }
    $pdf->SetTextColor(...cert_hex_rgb((string)$n['color']));
    $pdf->SetXY($W * $n['x'] / 100, $H * $n['y'] / 100);
    $pdf->Cell($nameW, $size * 0.5, $name, 0, 0, $n['align']);

    // Texto do corpo
    $b = $layout['body'];
    $pdf->SetFont('Helvetica', '', (float)$b['size']);
    $pdf->SetTextColor(...cert_hex_rgb((string)$b['color']));
    $pdf->SetXY($W * $b['x'] / 100, $H * $b['y'] / 100);
    $pdf->MultiCell($W * $b['w'] / 100, (float)$b['size'] * 0.5, cert_pdf_text(cert_body_text($c, (string)($tpl['body_text'] ?: cert_default_body()))), 0, $b['align']);

    // Assinaturas
    foreach (['sig1', 'sig2'] as $k) {
        cert_draw_signer($pdf, $signers[$k] ?? null, $W * $layout[$k]['x'] / 100, $H * $layout[$k]['y'] / 100, $W * $layout[$k]['w'] / 100);
    }

    // QR + código + link
    $url = cert_verify_url((string)$c['code']);
    $q = $layout['qr'];
    $box = $W * $q['w'] / 100;
    cert_draw_qr($pdf, $url, $W * $q['x'] / 100, $H * $q['y'] / 100, $box);
    $cd = $layout['code'];
    $pdf->SetFont('Helvetica', '', (float)$cd['size']);
    $pdf->SetTextColor(...cert_hex_rgb((string)$cd['color']));
    $pdf->SetXY($W * $cd['x'] / 100, $H * $cd['y'] / 100);
    $pdf->MultiCell($W * $cd['w'] / 100, (float)$cd['size'] * 0.45,
        cert_pdf_text("Código: {$c['code']}\nVerifique a autenticidade em {$url}"), 0, $cd['align']);

    return $pdf->Output('S');
}

/** FPDI com os metadados do certificado (usado ao carimbar PDFs prontos). */
class CertFpdi extends \setasign\Fpdi\Fpdi {}

/**
 * Carimba um PDF pronto com QR, código, link e assinatura na posição escolhida.
 * $pos: page (0 = todas as páginas), x/y = canto superior esquerdo do selo e w = largura, todos em % da página.
 * @return array{0:?string,1:?string} [bytes, erro]
 */
function cert_stamp_pdf(string $srcPath, array $c, ?array $signer, array $pos): array {
    $page = max(0, (int)($pos['page'] ?? 1));
    $w = max(14.0, min(60.0, (float)($pos['w'] ?? 22)));
    $x = max(0.0, min(100.0, (float)($pos['x'] ?? 70)));
    $y = max(0.0, min(100.0, (float)($pos['y'] ?? 85)));

    $pdf = new CertFpdi();
    $pdf->SetCompression(true);
    $pdf->SetTitle('Certificado ' . $c['code']);
    $pdf->SetSubject((string)$c['code']);
    $pdf->SetCreator('CoBraLT');
    $pdf->SetAutoPageBreak(false);
    try {
        $count = $pdf->setSourceFile($srcPath);
        if ($page > $count) {
            return [null, "O PDF tem apenas {$count} página(s)."];
        }
        for ($i = 1; $i <= $count; $i++) {
            $tplId = $pdf->importPage($i);
            $size = $pdf->getTemplateSize($tplId);
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($tplId);
            if ($page === 0 || $page === $i) {
                $W = (float)$size['width'];
                $H = (float)$size['height'];
                $bw = $W * $w / 100;
                $px = max(0.0, min($W - $bw, $W * $x / 100));
                $py = max(0.0, min($H - $bw * 0.3, $H * $y / 100));
                cert_draw_stamp($pdf, $c, $signer, $px, $py, $bw);
            }
        }
        return [$pdf->Output('S'), null];
    } catch (Throwable $e) {
        error_log('[CoBraLT] carimbo de PDF: ' . $e->getMessage());
        return [null, 'Não foi possível ler este PDF (pode estar protegido ou em formato não suportado). Salve-o novamente como PDF comum, ou use um modelo com a arte em imagem.'];
    }
}

/** Selo de verificação de $bw mm de largura (proporção 100:30) com canto superior esquerdo em ($x,$y). */
function cert_draw_stamp(FPDF $pdf, array $c, ?array $signer, float $x, float $y, float $bw): void {
    $scale = $bw / 100;
    $bh = 30 * $scale;

    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetDrawColor(0, 43, 78);
    $pdf->SetLineWidth(0.25);
    $pdf->Rect($x, $y, $bw, $bh, 'DF');

    $qrBox = $bh - 4 * $scale;
    cert_draw_qr($pdf, cert_verify_url((string)$c['code']), $x + 2 * $scale, $y + 2 * $scale, $qrBox);

    $tx = $x + $qrBox + 5 * $scale;
    $tw = $bw - $qrBox - 7 * $scale;
    if ($signer) {
        $img = cert_asset_path($signer['image_file'] ?? null);
        if ($img) {
            try { $pdf->Image($img, $tx, $y + 1.5 * $scale, 0, 9 * $scale); } catch (Throwable $e) { /* segue sem imagem */ }
        }
        $pdf->SetTextColor(30, 30, 30);
        $pdf->SetFont('Helvetica', 'B', max(4.5, 7.5 * $scale * 1.15));
        $pdf->SetXY($tx, $y + 11 * $scale);
        $pdf->Cell($tw, 3.5 * $scale, cert_pdf_text((string)$signer['name']), 0, 2, 'L');
        if (!empty($signer['role'])) {
            $pdf->SetFont('Helvetica', '', max(4, 6.5 * $scale * 1.15));
            $pdf->Cell($tw, 3 * $scale, cert_pdf_text((string)$signer['role']), 0, 2, 'L');
        }
    }
    $pdf->SetTextColor(0, 43, 78);
    $pdf->SetFont('Helvetica', 'B', max(4.5, 7 * $scale * 1.15));
    $pdf->SetXY($tx, $y + $bh - 11 * $scale);
    $pdf->Cell($tw, 3.5 * $scale, cert_pdf_text('Código: ' . $c['code']), 0, 2, 'L');
    $pdf->SetFont('Helvetica', '', max(3.8, 5.8 * $scale * 1.15));
    $pdf->SetTextColor(60, 60, 60);
    $pdf->MultiCell($tw, 2.8 * $scale, cert_pdf_text('Verifique em ' . preg_replace('#^https?://#', '', cert_verify_url((string)$c['code']))), 0, 'L');
}

function cert_load_template(PDO $db, int $id): ?array {
    $st = $db->prepare('SELECT * FROM certificate_templates WHERE id = ?');
    $st->execute([$id]);
    return $st->fetch() ?: null;
}

function cert_load_signer(PDO $db, ?int $id): ?array {
    if (!$id) return null;
    $st = $db->prepare('SELECT * FROM certificate_signers WHERE id = ?');
    $st->execute([$id]);
    return $st->fetch() ?: null;
}

/** Gera os bytes do PDF de um certificado conforme seu modelo (ou o layout padrão). */
function cert_render_for(PDO $db, array $cert): string {
    $tpl = !empty($cert['template_id']) ? cert_load_template($db, (int)$cert['template_id']) : null;
    if (!$tpl) {
        return cert_pdf($cert);
    }
    return cert_render_template($cert, $tpl, [
        'sig1' => cert_load_signer($db, isset($tpl['signer1_id']) ? (int)$tpl['signer1_id'] : null),
        'sig2' => cert_load_signer($db, isset($tpl['signer2_id']) ? (int)$tpl['signer2_id'] : null),
    ]);
}

/** Grava o PDF em storage/certificados/CODIGO.pdf e o SHA-256 no banco. */
function cert_store_pdf(PDO $db, array $cert, string $bytes, string $source): array {
    $file = $cert['code'] . '.pdf';
    if (file_put_contents(cert_storage_dir() . '/' . $file, $bytes, LOCK_EX) === false) {
        throw new RuntimeException('Não foi possível gravar o PDF.');
    }
    $db->prepare('UPDATE certificates SET pdf_file = ?, pdf_sha256 = ?, source = ? WHERE id = ?')
       ->execute([$file, hash('sha256', $bytes), $source, $cert['id']]);
    return cert_find($db, (string)$cert['code']) ?? $cert;
}

/** Emite pelo modelo (ou layout padrão se $templateId = 0) e guarda o PDF. */
function cert_issue_stored(PDO $db, array $data, ?int $issuedBy, int $templateId = 0): array {
    $data['template_id'] = $templateId ?: null;
    $cert = cert_insert($db, $data, $issuedBy);
    if ($templateId) {
        $db->prepare('UPDATE certificates SET template_id = ? WHERE id = ?')->execute([$templateId, $cert['id']]);
        $cert['template_id'] = $templateId;
    }
    return cert_store_pdf($db, $cert, cert_render_for($db, $cert), $templateId ? 'modelo' : 'padrao');
}

/** PDF guardado (ou, para registros antigos sem arquivo, gerado agora e guardado). */
function cert_get_pdf(PDO $db, array $cert): string {
    $path = !empty($cert['pdf_file']) ? cert_storage_dir() . '/' . basename((string)$cert['pdf_file']) : '';
    if ($path && is_file($path)) {
        $bytes = (string)file_get_contents($path);
        // PDFs gerados por nós com outro endereço do site (ex.: SITE_URL errado) são refeitos ao baixar.
        // PDFs assinados a partir de um arquivo enviado ('pdf') não podem ser regenerados.
        $host = (string)parse_url(cert_base_url(), PHP_URL_HOST);
        if (($cert['source'] ?? '') === 'pdf' || $host === '' || str_contains($bytes, $host)) {
            return $bytes;
        }
    }
    $bytes = cert_render_for($db, $cert);
    cert_store_pdf($db, $cert, $bytes, (string)($cert['source'] ?: 'padrao'));
    return $bytes;
}
