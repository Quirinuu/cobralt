<?php
/**
 * includes/certificate_pdf.php
 * Gera o PDF (A4 paisagem) de um certificado, com QR apontando para /verificar.
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/fpdf/fpdf.php';
require_once dirname(__DIR__) . '/vendor/qrcode/qrcode.php';
require_once __DIR__ . '/certificates.php';

/** FPDF usa Latin-1/CP1252; português cabe inteiro nele. */
function cert_pdf_text(string $s): string {
    $out = @iconv('UTF-8', 'windows-1252//TRANSLIT//IGNORE', $s);
    return $out === false ? $s : $out;
}

function cert_pdf(array $c): string {
    $navy = [0, 43, 78];
    $pdf = new FPDF('L', 'mm', 'A4');
    $pdf->SetAutoPageBreak(false);
    $pdf->AddPage();

    $pdf->SetDrawColor(...$navy);
    $pdf->SetLineWidth(1.6);
    $pdf->Rect(8, 8, 281, 194);
    $pdf->SetLineWidth(0.3);
    $pdf->Rect(12, 12, 273, 186);

    try {
        $pdf->Image(dirname(__DIR__) . '/assets/img/logo.png', 133, 17, 30);
    } catch (Throwable $e) {
        // logo com canal alfa não é suportado pelo FPDF; segue sem logo
    }

    $pdf->SetTextColor(...$navy);
    $pdf->SetFont('Helvetica', 'B', 34);
    $pdf->SetXY(0, 52);
    $pdf->Cell(297, 14, 'CERTIFICADO', 0, 1, 'C');

    $pdf->SetFont('Helvetica', '', 13);
    $pdf->SetTextColor(60, 60, 60);
    $pdf->SetXY(0, 72);
    $pdf->Cell(297, 8, cert_pdf_text('O Comitê Brasileiro das Ligas do Trauma (CoBraLT) certifica que'), 0, 1, 'C');

    $name = cert_pdf_text(mb_strtoupper((string)$c['holder_name'], 'UTF-8'));
    $size = 28;
    $pdf->SetFont('Helvetica', 'B', $size);
    while ($size > 12 && $pdf->GetStringWidth($name) > 250) {
        $size--;
        $pdf->SetFont('Helvetica', 'B', $size);
    }
    $pdf->SetTextColor(...$navy);
    $pdf->SetXY(0, 86);
    $pdf->Cell(297, 14, $name, 0, 1, 'C');

    $verbs = [
        'participante' => 'participou do evento',
        'palestrante'  => 'atuou como palestrante no evento',
        'organizador'  => 'atuou na organização do evento',
        'monitor'      => 'atuou como monitor no evento',
    ];
    $text = ($verbs[$c['cert_type']] ?? 'participou do evento') . ' ' . $c['event_name'];
    if (!empty($c['hours'])) {
        $text .= ', com carga horária de ' . (int)$c['hours'] . ' hora' . ((int)$c['hours'] > 1 ? 's' : '');
    }
    $text .= '.';
    $pdf->SetFont('Helvetica', '', 14);
    $pdf->SetTextColor(60, 60, 60);
    $pdf->SetXY(35, 108);
    $pdf->MultiCell(227, 8, cert_pdf_text($text), 0, 'C');

    $date = DateTime::createFromFormat('Y-m-d', (string)$c['issued_at']);
    $pdf->SetFont('Helvetica', '', 11);
    $pdf->SetXY(20, 172);
    $pdf->Cell(180, 6, cert_pdf_text('Emitido em ' . ($date ? $date->format('d/m/Y') : $c['issued_at'])), 0, 2, 'L');
    $pdf->Cell(180, 6, cert_pdf_text('Código: ' . $c['code']), 0, 2, 'L');
    $pdf->SetFont('Helvetica', '', 9);
    $pdf->Cell(180, 5, cert_pdf_text('Verifique a autenticidade em ' . cert_verify_url((string)$c['code'])), 0, 0, 'L');

    // QR code desenhado como retângulos (não depende da extensão GD).
    $qr = QRCode::getMinimumQRCode(cert_verify_url((string)$c['code']), QR_ERROR_CORRECT_LEVEL_M);
    $n = $qr->getModuleCount();
    $box = 30.0;
    $cell = $box / ($n + 2);
    $x0 = 285 - $box - 6;
    $y0 = 198 - $box - 6;
    $pdf->SetFillColor(0, 0, 0);
    for ($r = 0; $r < $n; $r++) {
        for ($k = 0; $k < $n; $k++) {
            if ($qr->isDark($r, $k)) {
                $pdf->Rect($x0 + ($k + 1) * $cell, $y0 + ($r + 1) * $cell, $cell, $cell, 'F');
            }
        }
    }

    return $pdf->Output('S');
}
