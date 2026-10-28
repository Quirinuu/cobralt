<?php
/**
 * includes/certificate_verify.php
 * Verificação de certificados por código, QR (via código) ou arquivo PDF.
 */

declare(strict_types=1);

require_once __DIR__ . '/certificates.php';
require_once __DIR__ . '/certificate_pdf.php';

const CERT_PDF_MAX_BYTES = 2 * 1024 * 1024;

/** Lê o código gravado em /Subject do PDF; '' se não houver. */
function cert_extract_code_from_pdf(string $bytes): string {
    if (preg_match('/\/Subject\s*\(\s*(CBL-[A-Z0-9-]{14})\s*\)/', $bytes, $m)) {
        return cert_normalize_code($m[1]);
    }
    return '';
}

/**
 * Confere um PDF enviado.
 * @return array{state:string,cert:?array,identical:bool,code:string}
 *   state: invalid | no_code | notfound | found
 *   identical: o arquivo é byte a byte o que o sistema emitiria para esse certificado
 */
function cert_verify_pdf(PDO $db, string $bytes): array {
    $out = ['state' => 'invalid', 'cert' => null, 'identical' => false, 'code' => ''];
    if ($bytes === '' || strlen($bytes) > CERT_PDF_MAX_BYTES || !str_starts_with($bytes, '%PDF-')) {
        return $out;
    }
    $code = cert_extract_code_from_pdf($bytes);
    if ($code === '') {
        $out['state'] = 'no_code';
        return $out;
    }
    $out['code'] = $code;
    $cert = cert_find($db, $code);
    if (!$cert) {
        $out['state'] = 'notfound';
        return $out;
    }
    $out['state'] = 'found';
    $out['cert'] = $cert;
    // PDFs emitidos guardam o SHA-256; registros antigos sem hash são comparados com o layout padrão regenerado.
    $expected = !empty($cert['pdf_sha256']) ? (string)$cert['pdf_sha256'] : hash('sha256', cert_pdf($cert));
    $out['identical'] = hash_equals($expected, hash('sha256', $bytes));
    return $out;
}

/** Lê o PDF enviado em $_FILES[$field]; devolve [bytes|null, erro|null]. */
function cert_read_uploaded_pdf(string $field = 'pdf'): array {
    $f = $_FILES[$field] ?? null;
    if (!$f || $f['error'] !== UPLOAD_ERR_OK) {
        return [null, 'Não foi possível receber o arquivo.'];
    }
    if ($f['size'] > CERT_PDF_MAX_BYTES) {
        return [null, 'O arquivo excede 2 MB.'];
    }
    $bytes = (string)file_get_contents($f['tmp_name']);
    return [$bytes, null]; // o arquivo nunca é gravado em disco
}

/** Card de resultado (público e admin). $identical = null quando a consulta não foi por PDF. */
function cert_render_card(array $cert, ?bool $identical = null, bool $admin = false): void {
    $valid = $cert['status'] === 'valido';
    $date  = DateTime::createFromFormat('Y-m-d', (string)$cert['issued_at']);
    $types = cert_types();
    $color = $valid ? '#10B981' : '#EF4444';
    ?>
    <div style="border:2px solid <?= $color ?>;border-radius:12px;padding:1.5rem;background:#fff;">
      <h2 style="font-family:var(--font-display,serif);color:<?= $valid ? '#047857' : '#B91C1C' ?>;margin:0 0 1rem;">
        <?= $valid ? 'Certificado válido' : 'Certificado revogado' ?>
      </h2>
      <?php if ($identical === true && $valid): ?>
        <p style="margin:0 0 1rem;color:#047857;"><strong>✔ O arquivo enviado é idêntico ao original emitido pelo CoBraLT.</strong></p>
      <?php elseif ($identical === false): ?>
        <p style="margin:0 0 1rem;color:#B45309;"><strong>⚠ O código existe, mas este arquivo é diferente do original emitido.</strong>
          Pode ter sido editado, ou gerado por outro programa. Confie nos dados oficiais abaixo e compare com o que está no seu documento.</p>
      <?php endif; ?>
      <dl style="display:grid;grid-template-columns:max-content 1fr;gap:.4rem 1.25rem;margin:0;">
        <dt><strong>Nome</strong></dt><dd style="margin:0"><?= htmlspecialchars((string)$cert['holder_name']) ?></dd>
        <dt><strong>Evento</strong></dt><dd style="margin:0"><?= htmlspecialchars((string)$cert['event_name']) ?></dd>
        <dt><strong>Tipo</strong></dt><dd style="margin:0"><?= htmlspecialchars($types[$cert['cert_type']] ?? (string)$cert['cert_type']) ?></dd>
        <?php if ($cert['hours']): ?><dt><strong>Carga horária</strong></dt><dd style="margin:0"><?= (int)$cert['hours'] ?> h</dd><?php endif; ?>
        <dt><strong>Emissão</strong></dt><dd style="margin:0"><?= htmlspecialchars($date ? $date->format('d/m/Y') : (string)$cert['issued_at']) ?></dd>
        <dt><strong>Código</strong></dt><dd style="margin:0"><?= htmlspecialchars((string)$cert['code']) ?></dd>
        <?php if (!$valid && $cert['revoked_at']): ?><dt><strong>Revogado em</strong></dt><dd style="margin:0"><?= htmlspecialchars(substr((string)$cert['revoked_at'], 0, 10)) ?></dd><?php endif; ?>
        <?php if ($admin): ?>
          <?php if ($cert['revoked_reason']): ?><dt><strong>Motivo</strong></dt><dd style="margin:0"><?= htmlspecialchars((string)$cert['revoked_reason']) ?></dd><?php endif; ?>
          <?php if ($cert['holder_email']): ?><dt><strong>E-mail</strong></dt><dd style="margin:0"><?= htmlspecialchars((string)$cert['holder_email']) ?></dd><?php endif; ?>
          <dt><strong>Registrado em</strong></dt><dd style="margin:0"><?= htmlspecialchars((string)$cert['created_at']) ?></dd>
        <?php endif; ?>
      </dl>
    </div>
    <?php
}
