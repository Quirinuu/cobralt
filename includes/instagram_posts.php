<?php
declare(strict_types=1);

/**
 * Busca posts recentes do Instagram com cache local.
 *
 * Configuração esperada no ambiente:
 * - INSTAGRAM_ACCESS_TOKEN: token de acesso da Meta/Instagram.
 * - INSTAGRAM_USER_ID: ID da conta profissional do Instagram.
 * - INSTAGRAM_GRAPH_VERSION: opcional. Padrão v20.0.
 */
function get_instagram_posts(int $limit = 6, int $ttlSeconds = 3600): array {
    $token = trim((string)(getenv('INSTAGRAM_ACCESS_TOKEN') ?: ''));
    if ($token === '') {
        return [];
    }

    $cacheFile = dirname(__DIR__) . '/storage/instagram-posts.json';
    $cached = instagram_read_cache($cacheFile);
    if ($cached && ($cached['fetched_at'] ?? 0) > (time() - $ttlSeconds)) {
        return array_slice($cached['posts'] ?? [], 0, $limit);
    }

    $posts = instagram_fetch_posts($token, $limit);
    if (!empty($posts)) {
        instagram_write_cache($cacheFile, $posts);
        return array_slice($posts, 0, $limit);
    }

    return array_slice($cached['posts'] ?? [], 0, $limit);
}

function instagram_fetch_posts(string $token, int $limit): array {
    $fields = 'id,caption,media_type,media_url,thumbnail_url,permalink,timestamp';
    $userId = trim((string)(getenv('INSTAGRAM_USER_ID') ?: ''));
    $version = trim((string)(getenv('INSTAGRAM_GRAPH_VERSION') ?: 'v20.0'));

    if ($userId === '') {
        return [];
    }

    $url = 'https://graph.facebook.com/' . rawurlencode($version) . '/' . rawurlencode($userId)
         . '/media?fields=' . rawurlencode($fields)
         . '&limit=' . max(1, min(25, $limit))
         . '&access_token=' . rawurlencode($token);

    $json = instagram_http_get($url);
    if ($json === '') {
        return [];
    }

    $payload = json_decode($json, true);
    if (!is_array($payload) || empty($payload['data']) || !is_array($payload['data'])) {
        return [];
    }

    $posts = [];
    foreach ($payload['data'] as $item) {
        if (!is_array($item)) {
            continue;
        }

        $caption = trim((string)($item['caption'] ?? ''));
        $title = instagram_caption_title($caption);
        $excerpt = instagram_caption_excerpt($caption);
        $mediaType = (string)($item['media_type'] ?? '');
        $image = (string)($item['thumbnail_url'] ?? $item['media_url'] ?? '');

        $posts[] = [
            'title' => $title,
            'excerpt' => $excerpt,
            'category' => $mediaType === 'VIDEO' ? 'Instagram Reels' : 'Instagram',
            'published_at' => (string)($item['timestamp'] ?? ''),
            'url' => (string)($item['permalink'] ?? INSTAGRAM_URL),
            'image' => $image,
            'source' => 'instagram',
            'external' => true,
        ];
    }

    return $posts;
}

function instagram_http_get(string $url): string {
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_USERAGENT => 'CoBraLT Website/1.0',
        ]);
        $body = curl_exec($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return ($status >= 200 && $status < 300 && is_string($body)) ? $body : '';
    }

    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 10,
            'header' => "User-Agent: CoBraLT Website/1.0\r\n",
        ],
    ]);
    $body = @file_get_contents($url, false, $context);
    return is_string($body) ? $body : '';
}

function instagram_read_cache(string $cacheFile): array {
    if (!is_file($cacheFile)) {
        return [];
    }
    $json = file_get_contents($cacheFile);
    $data = is_string($json) ? json_decode($json, true) : null;
    return is_array($data) ? $data : [];
}

function instagram_write_cache(string $cacheFile, array $posts): void {
    $dir = dirname($cacheFile);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    file_put_contents($cacheFile, json_encode([
        'fetched_at' => time(),
        'posts' => $posts,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
}

function instagram_caption_title(string $caption): string {
    $caption = trim(preg_replace('/\s+/', ' ', $caption) ?? '');
    if ($caption === '') {
        return 'Post do Instagram';
    }
    $title = preg_split('/(?<=[.!?])\s+/', $caption, 2)[0] ?? $caption;
    return instagram_trim_text($title, 92);
}

function instagram_caption_excerpt(string $caption): string {
    $caption = trim(preg_replace('/\s+/', ' ', $caption) ?? '');
    if ($caption === '') {
        return 'Acompanhe esta publicação no Instagram oficial do CoBraLT.';
    }
    return instagram_trim_text($caption, 170);
}

function instagram_trim_text(string $text, int $limit): string {
    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        return mb_strlen($text) > $limit ? rtrim(mb_substr($text, 0, $limit - 3)) . '...' : $text;
    }
    return strlen($text) > $limit ? rtrim(substr($text, 0, $limit - 3)) . '...' : $text;
}
