<?php
/**
 * Block based page builder helpers.
 *
 * Pages continue to use the existing `pages.content` column. New pages store a
 * JSON document there; old Quill HTML is rendered as a legacy rich text block.
 */

declare(strict_types=1);

const PB_VERSION = 1;

function pb_h(?string $value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function pb_is_list(array $array): bool {
    $i = 0;
    foreach ($array as $key => $_) {
        if ($key !== $i++) return false;
    }
    return true;
}

function pb_bool($value): bool {
    return $value === true || $value === 1 || $value === '1' || $value === 'true' || $value === 'on';
}

function pb_pick($value, array $allowed, string $default): string {
    $value = is_string($value) ? $value : '';
    return in_array($value, $allowed, true) ? $value : $default;
}

function pb_color($value, string $default): string {
    $value = trim((string)$value);
    if (preg_match('/^#(?:[0-9a-f]{3}|[0-9a-f]{6})$/i', $value)) {
        return $value;
    }
    return $default;
}

function pb_text($value, int $max = 4000): string {
    $value = trim(strip_tags((string)$value));
    if (mb_strlen($value, 'UTF-8') > $max) {
        return mb_substr($value, 0, $max, 'UTF-8');
    }
    return $value;
}

function pb_url($value): string {
    $value = trim((string)$value);
    if ($value === '') return '';
    if (preg_match('/^\s*javascript:/i', $value)) return '';
    if (preg_match('/^(https?:\/\/|mailto:|tel:|\/|#|\.\.?\/)/i', $value)) return $value;
    if (preg_match('/^[a-z0-9_\-\/.]+(?:\?[a-z0-9_\-=&%.]+)?(?:#[a-z0-9_\-]+)?$/i', $value)) return $value;
    return '';
}

function pb_html($html): string {
    $html = trim((string)$html);
    $allowed = '<p><br><strong><b><em><i><u><s><strike><blockquote><ol><ul><li><a><h2><h3><h4>';
    $html = strip_tags($html, $allowed);
    $html = preg_replace('/\s+on[a-z]+\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/i', '', $html) ?? $html;
    $html = preg_replace('/\s+style\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/i', '', $html) ?? $html;
    $html = preg_replace('/href\s*=\s*([\'"])\s*javascript:[^\'"]*\1/i', 'href="#"', $html) ?? $html;
    return trim($html);
}

function pb_clean_items($items, string $kind): array {
    if (!is_array($items)) return [];
    $out = [];
    foreach ($items as $item) {
        if (!is_array($item)) continue;
        if ($kind === 'stats') {
            $value = pb_text($item['value'] ?? '', 80);
            $label = pb_text($item['label'] ?? '', 160);
            if ($value === '' && $label === '') continue;
            $out[] = ['value' => $value, 'label' => $label];
            continue;
        }
        if ($kind === 'gallery') {
            $image = pb_url($item['image_url'] ?? '');
            if ($image === '') continue;
            $out[] = [
                'image_url' => $image,
                'image_alt' => pb_text($item['image_alt'] ?? '', 160),
                'title' => pb_text($item['title'] ?? '', 160),
            ];
            continue;
        }
        $title = pb_text($item['title'] ?? '', 180);
        $text = pb_text($item['text'] ?? '', 800);
        if ($title === '' && $text === '') continue;
        $out[] = [
            'icon' => pb_text($item['icon'] ?? '', 24),
            'title' => $title,
            'text' => $text,
            'button_label' => pb_text($item['button_label'] ?? '', 80),
            'button_url' => pb_url($item['button_url'] ?? ''),
        ];
    }
    return array_slice($out, 0, 30);
}

function pb_base_block(array $block, string $type): array {
    return [
        'id' => pb_text($block['id'] ?? ('block-' . bin2hex(random_bytes(4))), 80),
        'type' => $type,
        'enabled' => array_key_exists('enabled', $block) ? pb_bool($block['enabled']) : true,
        'background' => pb_color($block['background'] ?? '#ffffff', '#ffffff'),
        'text_color' => pb_color($block['text_color'] ?? '#1E293B', '#1E293B'),
        'accent_color' => pb_color($block['accent_color'] ?? '#0EA5E9', '#0EA5E9'),
        'padding' => pb_pick($block['padding'] ?? 'normal', ['compact', 'normal', 'spacious'], 'normal'),
        'align' => pb_pick($block['align'] ?? 'left', ['left', 'center'], 'left'),
        'width' => pb_pick($block['width'] ?? 'default', ['narrow', 'default', 'wide'], 'default'),
    ];
}

function pb_normalize_block($block): ?array {
    if (!is_array($block)) return null;
    $type = pb_pick($block['type'] ?? 'rich_text', ['hero', 'rich_text', 'cards', 'image_text', 'stats', 'cta', 'gallery', 'html'], 'rich_text');
    $base = pb_base_block($block, $type);

    if ($type === 'hero') {
        return $base + [
            'eyebrow' => pb_text($block['eyebrow'] ?? '', 160),
            'title' => pb_text($block['title'] ?? '', 220),
            'text' => pb_text($block['text'] ?? '', 1400),
            'show_button' => pb_bool($block['show_button'] ?? false),
            'button_label' => pb_text($block['button_label'] ?? '', 80),
            'button_url' => pb_url($block['button_url'] ?? ''),
            'show_second_button' => pb_bool($block['show_second_button'] ?? false),
            'second_button_label' => pb_text($block['second_button_label'] ?? '', 80),
            'second_button_url' => pb_url($block['second_button_url'] ?? ''),
            'show_image' => pb_bool($block['show_image'] ?? false),
            'image_url' => pb_url($block['image_url'] ?? ''),
            'image_alt' => pb_text($block['image_alt'] ?? '', 160),
        ];
    }

    if ($type === 'cards') {
        return $base + [
            'eyebrow' => pb_text($block['eyebrow'] ?? '', 160),
            'title' => pb_text($block['title'] ?? '', 220),
            'text' => pb_text($block['text'] ?? '', 1200),
            'columns' => pb_pick($block['columns'] ?? '3', ['2', '3', '4'], '3'),
            'items' => pb_clean_items($block['items'] ?? [], 'cards'),
        ];
    }

    if ($type === 'image_text') {
        return $base + [
            'eyebrow' => pb_text($block['eyebrow'] ?? '', 160),
            'title' => pb_text($block['title'] ?? '', 220),
            'text' => pb_text($block['text'] ?? '', 1800),
            'image_url' => pb_url($block['image_url'] ?? ''),
            'image_alt' => pb_text($block['image_alt'] ?? '', 160),
            'image_position' => pb_pick($block['image_position'] ?? 'right', ['left', 'right'], 'right'),
            'show_button' => pb_bool($block['show_button'] ?? false),
            'button_label' => pb_text($block['button_label'] ?? '', 80),
            'button_url' => pb_url($block['button_url'] ?? ''),
        ];
    }

    if ($type === 'stats') {
        return $base + [
            'eyebrow' => pb_text($block['eyebrow'] ?? '', 160),
            'title' => pb_text($block['title'] ?? '', 220),
            'text' => pb_text($block['text'] ?? '', 900),
            'items' => pb_clean_items($block['items'] ?? [], 'stats'),
        ];
    }

    if ($type === 'cta') {
        return $base + [
            'eyebrow' => pb_text($block['eyebrow'] ?? '', 160),
            'title' => pb_text($block['title'] ?? '', 220),
            'text' => pb_text($block['text'] ?? '', 1200),
            'show_button' => pb_bool($block['show_button'] ?? true),
            'button_label' => pb_text($block['button_label'] ?? '', 80),
            'button_url' => pb_url($block['button_url'] ?? ''),
        ];
    }

    if ($type === 'gallery') {
        return $base + [
            'eyebrow' => pb_text($block['eyebrow'] ?? '', 160),
            'title' => pb_text($block['title'] ?? '', 220),
            'text' => pb_text($block['text'] ?? '', 900),
            'items' => pb_clean_items($block['items'] ?? [], 'gallery'),
        ];
    }

    if ($type === 'html') {
        return $base + ['html' => pb_html($block['html'] ?? '')];
    }

    return $base + [
        'show_title' => pb_bool($block['show_title'] ?? false),
        'title' => pb_text($block['title'] ?? '', 220),
        'body' => pb_html($block['body'] ?? ''),
    ];
}

function pb_default_blocks(string $title = ''): array {
    return [
        [
            'id' => 'hero-' . bin2hex(random_bytes(3)),
            'type' => 'hero',
            'enabled' => true,
            'background' => '#002B4E',
            'text_color' => '#ffffff',
            'accent_color' => '#38BDF8',
            'padding' => 'spacious',
            'align' => 'center',
            'width' => 'wide',
            'eyebrow' => 'CoBraLT',
            'title' => $title ?: 'Nova pagina',
            'text' => 'Edite este bloco no painel administrativo.',
            'show_button' => false,
            'button_label' => '',
            'button_url' => '',
            'show_second_button' => false,
            'second_button_label' => '',
            'second_button_url' => '',
            'show_image' => false,
            'image_url' => '',
            'image_alt' => '',
        ],
        [
            'id' => 'text-' . bin2hex(random_bytes(3)),
            'type' => 'rich_text',
            'enabled' => true,
            'background' => '#ffffff',
            'text_color' => '#1E293B',
            'accent_color' => '#0EA5E9',
            'padding' => 'normal',
            'align' => 'left',
            'width' => 'narrow',
            'show_title' => true,
            'title' => 'Conteudo',
            'body' => '<p>Comece escrevendo aqui o conteudo da pagina.</p>',
        ],
    ];
}

function pb_decode_content(?string $content, string $fallbackTitle = ''): array {
    $content = trim((string)$content);
    if ($content === '') {
        return ['builder_version' => PB_VERSION, 'blocks' => pb_default_blocks($fallbackTitle)];
    }

    $decoded = json_decode($content, true);
    if (is_array($decoded)) {
        $rawBlocks = null;
        if (isset($decoded['blocks']) && is_array($decoded['blocks'])) {
            $rawBlocks = $decoded['blocks'];
        } elseif (pb_is_list($decoded)) {
            $rawBlocks = $decoded;
        }

        if (is_array($rawBlocks)) {
            $blocks = [];
            foreach ($rawBlocks as $block) {
                $clean = pb_normalize_block($block);
                if ($clean) $blocks[] = $clean;
            }
            return ['builder_version' => PB_VERSION, 'blocks' => $blocks ?: pb_default_blocks($fallbackTitle)];
        }
    }

    return [
        'builder_version' => PB_VERSION,
        'blocks' => [
            [
                'id' => 'legacy-' . bin2hex(random_bytes(3)),
                'type' => 'rich_text',
                'enabled' => true,
                'background' => '#ffffff',
                'text_color' => '#1E293B',
                'accent_color' => '#0EA5E9',
                'padding' => 'normal',
                'align' => 'left',
                'width' => 'narrow',
                'show_title' => false,
                'title' => '',
                'body' => pb_html($content),
            ],
        ],
    ];
}

function pb_encode_content($json, string $fallbackTitle = ''): string {
    $decoded = json_decode((string)$json, true);
    if (isset($decoded['blocks']) && is_array($decoded['blocks'])) {
        $decoded = $decoded['blocks'];
    }
    if (!is_array($decoded)) {
        $decoded = pb_default_blocks($fallbackTitle);
    }

    $blocks = [];
    foreach ($decoded as $block) {
        $clean = pb_normalize_block($block);
        if ($clean) $blocks[] = $clean;
    }

    return json_encode(
        ['builder_version' => PB_VERSION, 'blocks' => $blocks ?: pb_default_blocks($fallbackTitle)],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
}

function pb_summary(array $blocks, string $fallback = ''): string {
    $pieces = [];
    foreach ($blocks as $block) {
        foreach (['title', 'text', 'body', 'html'] as $key) {
            if (!empty($block[$key])) $pieces[] = strip_tags((string)$block[$key]);
        }
    }
    $summary = trim(preg_replace('/\s+/', ' ', implode(' ', $pieces)) ?? '');
    if ($summary === '') $summary = $fallback;
    return mb_substr($summary, 0, 155, 'UTF-8');
}

function pb_target_attrs(string $url): string {
    return preg_match('/^https?:\/\//i', $url) ? ' target="_blank" rel="noopener noreferrer"' : '';
}

function pb_block_style(array $block): string {
    return '--pb-bg:' . pb_h($block['background'] ?? '#ffffff')
        . ';--pb-text:' . pb_h($block['text_color'] ?? '#1E293B')
        . ';--pb-accent:' . pb_h($block['accent_color'] ?? '#0EA5E9') . ';';
}

function pb_block_class(array $block, string $extra): string {
    $padding = pb_pick($block['padding'] ?? 'normal', ['compact', 'normal', 'spacious'], 'normal');
    $align = pb_pick($block['align'] ?? 'left', ['left', 'center'], 'left');
    $width = pb_pick($block['width'] ?? 'default', ['narrow', 'default', 'wide'], 'default');
    return trim('pb-block ' . $extra . ' pb-pad-' . $padding . ' pb-align-' . $align . ' pb-width-' . $width);
}

function pb_button(string $label, string $url, string $style = 'primary'): void {
    if ($label === '' || $url === '') return;
    $class = $style === 'secondary' ? 'pb-btn pb-btn-secondary' : 'pb-btn pb-btn-primary';
    echo '<a class="' . $class . '" href="' . pb_h($url) . '"' . pb_target_attrs($url) . '>' . pb_h($label) . '</a>';
}

function pb_render_blocks(array $blocks): void {
    foreach ($blocks as $block) {
        if (empty($block['enabled'])) continue;
        $type = $block['type'] ?? 'rich_text';
        if ($type === 'hero') {
            pb_render_hero($block);
        } elseif ($type === 'cards') {
            pb_render_cards($block);
        } elseif ($type === 'image_text') {
            pb_render_image_text($block);
        } elseif ($type === 'stats') {
            pb_render_stats($block);
        } elseif ($type === 'cta') {
            pb_render_cta($block);
        } elseif ($type === 'gallery') {
            pb_render_gallery($block);
        } elseif ($type === 'html') {
            pb_render_html($block);
        } else {
            pb_render_rich_text($block);
        }
    }
}

function pb_render_eyebrow(array $block): void {
    if (!empty($block['eyebrow'])) {
        echo '<div class="pb-eyebrow">' . pb_h($block['eyebrow']) . '</div>';
    }
}

function pb_render_hero(array $block): void { ?>
<section class="<?= pb_h(pb_block_class($block, 'pb-hero')) ?>" style="<?= pb_block_style($block) ?>">
  <div class="pb-inner">
    <div class="pb-hero-grid<?= !empty($block['show_image']) && !empty($block['image_url']) ? '' : ' pb-hero-grid-single' ?>">
      <div class="pb-copy">
        <?php pb_render_eyebrow($block); ?>
        <?php if (!empty($block['title'])): ?><h1><?= pb_h($block['title']) ?></h1><?php endif; ?>
        <?php if (!empty($block['text'])): ?><p class="pb-lead"><?= nl2br(pb_h($block['text'])) ?></p><?php endif; ?>
        <div class="pb-actions">
          <?php if (!empty($block['show_button'])) pb_button($block['button_label'] ?? '', $block['button_url'] ?? '', 'primary'); ?>
          <?php if (!empty($block['show_second_button'])) pb_button($block['second_button_label'] ?? '', $block['second_button_url'] ?? '', 'secondary'); ?>
        </div>
      </div>
      <?php if (!empty($block['show_image']) && !empty($block['image_url'])): ?>
      <figure class="pb-media">
        <img src="<?= pb_h($block['image_url']) ?>" alt="<?= pb_h($block['image_alt'] ?? '') ?>" loading="lazy">
      </figure>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php }

function pb_render_rich_text(array $block): void { ?>
<section class="<?= pb_h(pb_block_class($block, 'pb-rich')) ?>" style="<?= pb_block_style($block) ?>">
  <div class="pb-inner">
    <?php if (!empty($block['show_title']) && !empty($block['title'])): ?><h2><?= pb_h($block['title']) ?></h2><?php endif; ?>
    <div class="post-content"><?= $block['body'] ?? '' ?></div>
  </div>
</section>
<?php }

function pb_render_cards(array $block): void {
    $columns = pb_pick($block['columns'] ?? '3', ['2', '3', '4'], '3');
?>
<section class="<?= pb_h(pb_block_class($block, 'pb-cards')) ?>" style="<?= pb_block_style($block) ?>">
  <div class="pb-inner">
    <div class="pb-section-head">
      <?php pb_render_eyebrow($block); ?>
      <?php if (!empty($block['title'])): ?><h2><?= pb_h($block['title']) ?></h2><?php endif; ?>
      <?php if (!empty($block['text'])): ?><p><?= nl2br(pb_h($block['text'])) ?></p><?php endif; ?>
    </div>
    <div class="pb-card-grid pb-cols-<?= pb_h($columns) ?>">
      <?php foreach (($block['items'] ?? []) as $item): ?>
      <article class="pb-card">
        <?php if (!empty($item['icon'])): ?><div class="pb-card-icon"><?= pb_h($item['icon']) ?></div><?php endif; ?>
        <?php if (!empty($item['title'])): ?><h3><?= pb_h($item['title']) ?></h3><?php endif; ?>
        <?php if (!empty($item['text'])): ?><p><?= nl2br(pb_h($item['text'])) ?></p><?php endif; ?>
        <?php if (!empty($item['button_label']) && !empty($item['button_url'])): ?>
          <a class="pb-text-link" href="<?= pb_h($item['button_url']) ?>"<?= pb_target_attrs($item['button_url']) ?>><?= pb_h($item['button_label']) ?></a>
        <?php endif; ?>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php }

function pb_render_image_text(array $block): void {
    $reverse = ($block['image_position'] ?? 'right') === 'left' ? ' pb-image-left' : '';
?>
<section class="<?= pb_h(pb_block_class($block, 'pb-image-text' . $reverse)) ?>" style="<?= pb_block_style($block) ?>">
  <div class="pb-inner">
    <div class="pb-split">
      <div class="pb-copy">
        <?php pb_render_eyebrow($block); ?>
        <?php if (!empty($block['title'])): ?><h2><?= pb_h($block['title']) ?></h2><?php endif; ?>
        <?php if (!empty($block['text'])): ?><p><?= nl2br(pb_h($block['text'])) ?></p><?php endif; ?>
        <div class="pb-actions"><?php if (!empty($block['show_button'])) pb_button($block['button_label'] ?? '', $block['button_url'] ?? '', 'primary'); ?></div>
      </div>
      <?php if (!empty($block['image_url'])): ?>
      <figure class="pb-media">
        <img src="<?= pb_h($block['image_url']) ?>" alt="<?= pb_h($block['image_alt'] ?? '') ?>" loading="lazy">
      </figure>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php }

function pb_render_stats(array $block): void { ?>
<section class="<?= pb_h(pb_block_class($block, 'pb-stats')) ?>" style="<?= pb_block_style($block) ?>">
  <div class="pb-inner">
    <div class="pb-section-head">
      <?php pb_render_eyebrow($block); ?>
      <?php if (!empty($block['title'])): ?><h2><?= pb_h($block['title']) ?></h2><?php endif; ?>
      <?php if (!empty($block['text'])): ?><p><?= nl2br(pb_h($block['text'])) ?></p><?php endif; ?>
    </div>
    <div class="pb-stat-grid">
      <?php foreach (($block['items'] ?? []) as $item): ?>
      <div class="pb-stat">
        <div class="pb-stat-value"><?= pb_h($item['value'] ?? '') ?></div>
        <div class="pb-stat-label"><?= pb_h($item['label'] ?? '') ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php }

function pb_render_cta(array $block): void { ?>
<section class="<?= pb_h(pb_block_class($block, 'pb-cta')) ?>" style="<?= pb_block_style($block) ?>">
  <div class="pb-inner">
    <?php pb_render_eyebrow($block); ?>
    <?php if (!empty($block['title'])): ?><h2><?= pb_h($block['title']) ?></h2><?php endif; ?>
    <?php if (!empty($block['text'])): ?><p><?= nl2br(pb_h($block['text'])) ?></p><?php endif; ?>
    <div class="pb-actions"><?php if (!empty($block['show_button'])) pb_button($block['button_label'] ?? '', $block['button_url'] ?? '', 'primary'); ?></div>
  </div>
</section>
<?php }

function pb_render_gallery(array $block): void { ?>
<section class="<?= pb_h(pb_block_class($block, 'pb-gallery')) ?>" style="<?= pb_block_style($block) ?>">
  <div class="pb-inner">
    <div class="pb-section-head">
      <?php pb_render_eyebrow($block); ?>
      <?php if (!empty($block['title'])): ?><h2><?= pb_h($block['title']) ?></h2><?php endif; ?>
      <?php if (!empty($block['text'])): ?><p><?= nl2br(pb_h($block['text'])) ?></p><?php endif; ?>
    </div>
    <div class="pb-gallery-grid">
      <?php foreach (($block['items'] ?? []) as $item): ?>
      <figure>
        <img src="<?= pb_h($item['image_url'] ?? '') ?>" alt="<?= pb_h($item['image_alt'] ?? '') ?>" loading="lazy">
        <?php if (!empty($item['title'])): ?><figcaption><?= pb_h($item['title']) ?></figcaption><?php endif; ?>
      </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php }

function pb_render_html(array $block): void { ?>
<section class="<?= pb_h(pb_block_class($block, 'pb-html')) ?>" style="<?= pb_block_style($block) ?>">
  <div class="pb-inner">
    <div class="post-content"><?= $block['html'] ?? '' ?></div>
  </div>
</section>
<?php }

function pb_render_managed_page_if_exists(string $slug, string $active = '', string $base = '../'): bool {
    $slug = preg_replace('/[^a-z0-9\-]/i', '', trim($slug)) ?? '';
    if ($slug === '') return false;

    if (!function_exists('getPublicDB')) {
        require_once __DIR__ . '/db.php';
    }
    if (!function_exists('layout_head')) {
        require_once __DIR__ . '/layout.php';
    }

    try {
        $db = getPublicDB();
        $stmt = $db->prepare("SELECT title, slug, content FROM pages WHERE slug = ? AND status = 'published' LIMIT 1");
        $stmt->execute([$slug]);
        $page = $stmt->fetch();
    } catch (Throwable $e) {
        return false;
    }

    if (!$page) return false;

    $builder = pb_decode_content($page['content'] ?? '', $page['title'] ?? '');
    layout_head($page['title'], pb_summary($builder['blocks'], $page['title']), $base);
    layout_header($active, $base);
    echo '<main id="main-content" class="pb-page">';
    pb_render_blocks($builder['blocks']);
    echo '</main>';
    layout_footer($base);
    return true;
}
