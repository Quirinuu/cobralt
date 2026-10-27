<?php
/**
 * includes/sanitize.php
 * Sanitizador único de HTML rico (editor de posts e construtor de páginas).
 */

declare(strict_types=1);

function sanitize_rich_html(string $html): string {
    $allowed = '<p><br><strong><b><em><i><u><s><strike><blockquote><ol><ul><li><a><h2><h3><h4>';
    $html = strip_tags($html, $allowed);

    // Reescreve cada tag do zero: só <a> mantém atributo (href validado).
    // Isso elimina on*=, style=, e variações como <a/onclick=...>.
    $html = preg_replace_callback(
        '/<(\/?)([a-z0-9]+)((?:"[^"]*"|\'[^\']*\'|[^\'">])*)>/i',
        static function (array $m): string {
            $tag = strtolower($m[2]);
            if ($m[1] === '/') {
                return '</' . $tag . '>';
            }
            if ($tag !== 'a') {
                return '<' . $tag . '>';
            }
            $href = '#';
            if (preg_match('/\bhref\s*=\s*("([^"]*)"|\'([^\']*)\'|([^\s"\'>]+))/i', $m[3], $h)) {
                $raw = html_entity_decode(trim($h[2] ?? $h[3] ?? $h[4] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $raw = preg_replace('/[\x00-\x20]+/', '', $raw) ?? '';
                if (preg_match('/^(https?:\/\/|mailto:|tel:|\/(?!\/)|#|\.\.?\/)/i', $raw)) {
                    $href = $raw;
                }
            }
            return '<a href="' . htmlspecialchars($href, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '" rel="noopener noreferrer">';
        },
        $html
    ) ?? '';

    return trim($html);
}
