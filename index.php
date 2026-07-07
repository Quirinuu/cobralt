<?php
/**
 * index.php — CoBraLT
 * Página inicial. Dados vindos do banco — edite pelo painel admin/.
 */
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/posts_helpers.php';
require_once __DIR__ . '/includes/instagram_posts.php';
require_once __DIR__ . '/includes/colt_editions.php';
require_once __DIR__ . '/includes/page_builder.php';

require_once __DIR__ . '/includes/layout.php';

if (pb_render_managed_page_if_exists('home', '', './')) { exit; }

// ─── Busca os dados do banco ───────────────────────────────
try {
    $db = getPublicDB();

    // Posts publicados mais recentes para fallback da home
    $stmtNoticias = $db->query(
        "SELECT title, excerpt, category, published_at, slug, cover_image
         FROM posts
         WHERE status = 'published'
         ORDER BY published_at DESC
         LIMIT 6"
    );
    $noticias = $stmtNoticias->fetchAll();

    // Eventos ativos, ordenados
    $stmtEventos = $db->query(
        "SELECT dia, mes, titulo, local, descricao, link, link_texto, link_externo
         FROM eventos
         WHERE ativo = 1
           AND titulo NOT LIKE '%Pré-CoLT%'
           AND titulo NOT LIKE '%Pre-CoLT%'
           AND titulo NOT LIKE '%COTREM%'
           AND titulo NOT LIKE '%XXVIII CoLT%'
         ORDER BY ordem ASC
         LIMIT 6"
    );
    $eventos = $stmtEventos->fetchAll();

    // Ligas ativas, ordenadas
    $stmtLigas = $db->query(
        "SELECT emoji, nome, descricao, contagem, link
         FROM ligas_regioes
         WHERE ativo = 1
         ORDER BY ordem ASC"
    );
    $ligas = $stmtLigas->fetchAll();

    // Diretoria ativa — trazemos todos e agrupamos no PHP
    $stmtDir = $db->query(
        "SELECT nome, cargo, especialidade, foto, grupo, bio
         FROM diretoria
         WHERE ativo = 1
         ORDER BY grupo, ordem ASC"
    );
    $dirRows = $stmtDir->fetchAll();

    // Agrupar diretoria por grupo
    $diretoria = [];
    foreach ($dirRows as $row) {
        $diretoria[$row['grupo']][] = $row;
    }

} catch (PDOException $e) {
    // Em caso de falha no banco, mostra seções vazias sem quebrar a página
    $noticias = $eventos = $ligas = [];
    $diretoria = [];
}

// Emoji de categoria para notícia
$catEmoji = [
    'Protocolos'    => '📰',
    'Educação'      => '🎓',
    'Internacional' => '🌍',
    'Pesquisa'      => '🔬',
    'Eventos'       => '📅',
    'Institucional' => '🏛️',
];

$instagramPosts = get_instagram_posts(6);
$homePosts = $instagramPosts;
if (empty($homePosts)) {
    $homePosts = array_map(static function (array $post): array {
        $cover = trim((string)($post['cover_image'] ?? ''));
        return [
            'title' => (string)($post['title'] ?? ''),
            'excerpt' => (string)($post['excerpt'] ?? ''),
            'category' => (string)($post['category'] ?? 'Post'),
            'published_at' => (string)($post['published_at'] ?? ''),
            'url' => 'pages/post.php?slug=' . rawurlencode((string)($post['slug'] ?? '')),
            'image' => $cover !== '' ? (preg_match('/^(https?:\/\/|\/)/i', $cover) ? $cover : ltrim($cover, '/')) : '',
            'source' => 'site',
            'external' => false,
        ];
    }, $noticias);
}

$homePastColts = array_slice(colt_editions_newest_first(), 0, 8);
?>
<?php
// Head padronizado — index usa base './'
layout_head_only('CoBraLT — Comitê Brasileiro das Ligas do Trauma', 'CoBraLT — Comitê Brasileiro das Ligas do Trauma. Conectando profissionais de saúde para salvar vidas.', './');
?>
<body id="inicio">
<?php layout_header('', './', '#inicio', 'pages/'); ?>
<main id="main-content">

<!-- ═══ HERO ═════════════════════════════════════════════ -->
<section class="hero" aria-label="Destaque">
  <div class="hero-bg" aria-hidden="true"></div>
  <div class="hero-grid" aria-hidden="true"></div>
  <div class="hero-inner">
    <div class="hero-content">
      <div class="hero-badge" aria-label="Rede Nacional de Ligas do Trauma no Brasil">
        <span class="hero-badge-dot" aria-hidden="true"></span>
        Rede Nacional — Ligas do Trauma no Brasil
      </div>
      <h1 class="hero-title">Conectando as <span>Ligas do Trauma</span> para salvar vidas</h1>
      <p class="hero-subtitle">O Comitê Brasileiro das Ligas do Trauma reúne profissionais de saúde de todas as regiões do Brasil em torno da excelência no atendimento ao paciente grave.</p>
      <div class="hero-actions">
        <a href="#filiacao" class="btn btn-cta">
          <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
          Afiliar sua Liga
        </a>
        <a href="#ligas" class="btn btn-secondary">Mostrar Ligas</a>
      </div>
      <div class="hero-programs-quick" aria-label="Programas oficiais em destaque">
        <a href="pages/programa-salvando-vidas-2026.php" class="hero-program-quick-item">
          <span>Salvando Vidas 2026</span>
          <strong>Maio a dezembro de 2026</strong>
        </a>
        <a href="pages/programa-party-brasil-2026.php" class="hero-program-quick-item">
          <span>P.A.R.T.Y. Brasil 2026</span>
          <strong>Edital de 10/04/2026</strong>
        </a>
        <a href="pages/programa-junho-laranja-2026.php" class="hero-program-quick-item">
          <span>Junho Laranja 2026</span>
          <strong>Trabalho seguro sem queimaduras</strong>
        </a>
      </div>
      <div class="hero-stats" role="list" style="display:flex;gap:1.75rem;margin-top:1.75rem;flex-wrap:wrap;">
        <div role="listitem" style="display:flex;flex-direction:column;align-items:flex-start;">
          <div class="hero-stat-value" style="font-size:2.2rem;font-weight:800;color:#fff;line-height:1;">180<span style="color:var(--sky-light);">+</span></div>
          <div class="hero-stat-label" style="font-size:0.74rem;color:rgba(255,255,255,0.55);text-transform:uppercase;letter-spacing:0.08em;margin-top:4px;">Instituições afiliadas</div>
        </div>
        <div style="width:1px;background:rgba(255,255,255,0.15);align-self:stretch;" aria-hidden="true"></div>
        <div role="listitem" style="display:flex;flex-direction:column;align-items:flex-start;">
          <div class="hero-stat-value" style="font-size:2.2rem;font-weight:800;color:#fff;line-height:1;">2<span style="color:var(--sky-light);">mil+</span></div>
          <div class="hero-stat-label" style="font-size:0.74rem;color:rgba(255,255,255,0.55);text-transform:uppercase;letter-spacing:0.08em;margin-top:4px;">Participantes no COLT</div>
        </div>
        <div style="width:1px;background:rgba(255,255,255,0.15);align-self:stretch;" aria-hidden="true"></div>
        <div role="listitem" style="display:flex;flex-direction:column;align-items:flex-start;">
          <div class="hero-stat-value" style="font-size:2.2rem;font-weight:800;color:#fff;line-height:1;">23<span style="color:var(--sky-light);">anos</span></div>
          <div class="hero-stat-label" style="font-size:0.74rem;color:rgba(255,255,255,0.55);text-transform:uppercase;letter-spacing:0.08em;margin-top:4px;">De história</div>
        </div>
      </div>
    </div>
    <div class="hero-visual hide-mobile" aria-label="Programas oficiais em destaque">
      <div class="hero-card-stack">
        <div class="hero-card-back"></div>
        <div class="hero-card hero-programs-card">
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:1.1rem;">
            <div style="width:34px;height:34px;background:rgba(14,165,233,0.2);border-radius:8px;display:flex;align-items:center;justify-content:center;">
              <svg width="17" height="17" fill="none" stroke="#38BDF8" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div>
              <div style="color:rgba(255,255,255,0.5);font-size:0.66rem;text-transform:uppercase;letter-spacing:0.08em;">Programas oficiais</div>
              <div style="color:#fff;font-weight:700;font-size:0.9rem;">Destaques 2026</div>
            </div>
          </div>
          <a href="pages/programa-salvando-vidas-2026.php" class="hero-program-row hero-program-row--primary">
            <span class="hero-program-kicker">Ciclo 2026</span>
            <strong>Programa Salvando Vidas</strong>
            <small>Maio a dezembro de 2026</small>
          </a>
          <a href="pages/programa-party-brasil-2026.php" class="hero-program-row">
            <span class="hero-program-kicker">Adesão 2026</span>
            <strong>Programa P.A.R.T.Y. Brasil</strong>
            <small>Edital de 10/04/2026</small>
          </a>
          <a href="pages/programa-junho-laranja-2026.php" class="hero-program-row hero-program-row--orange">
            <span class="hero-program-kicker">Junho Laranja</span>
            <strong>Prevenção e Extensão</strong>
            <small>Trabalho seguro sem queimaduras</small>
          </a>
          <a href="#projetos" class="btn btn-event">Ver projetos</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ APOIADORES ══════════════════════════════════════════ -->
<section class="section" id="apoiadores" style="padding-top:2.5rem;padding-bottom:2.5rem;background:var(--off-white);overflow:hidden;" aria-labelledby="apoiadores-label">
  <div class="section-inner" style="max-width:1100px;">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.5rem;flex-wrap:wrap;gap:0.75rem;" data-animate>
      <div>
        <div class="divider" aria-hidden="true" style="margin-bottom:0.75rem;"></div>
        <span class="section-label" id="apoiadores-label">Comitê Científico</span>
        <h2 class="section-title" style="margin:0.2rem 0 0;">Nossos Apoiadores</h2>
      </div>
      <a href="pages/apoiadores.php" class="btn btn-secondary">
        Ver todos →
      </a>
    </div>

    <!-- Carrossel de apoiadores -->
    <div class="sup-carousel-outer index-carousel" data-index-carousel data-carousel-step="3" data-carousel-autoplay="2800">
      <button class="sup-carousel-btn index-carousel-btn" type="button" data-carousel-prev aria-label="Anterior">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
      </button>
      <div class="sup-viewport">
        <div class="sup-track index-carousel-track" id="supTrack" data-carousel-track>
          <?php
          $apoiadores = [
            ['nome'=>'Amauri Clemente da Rocha',           'foto'=>'amauri-clemente'],
            ['nome'=>'André Canesso Pierro',                'foto'=>'andre-pierro'],
            ['nome'=>'Andrea de Melo Alexandre Fraga',      'foto'=>'andrea-fraga'],
            ['nome'=>'Antonio Toshimitsu Onimaru',          'foto'=>'antonio-toshimitsu'],
            ['nome'=>'Dr. Caio Duarte',                     'foto'=>'caio-duarte'],
            ['nome'=>'Cesar Vanderlei Carmona',             'foto'=>'cesar-vanderlei'],
            ['nome'=>'Cláudio Diunky Okawa',                'foto'=>'claudio-diunky'],
            ['nome'=>'Cristhian Jaillita Meneses',          'foto'=>'cristhian-jaillita'],
            ['nome'=>'Fernando Antonio C. Spencer Netto',   'foto'=>'fernando-antonio'],
            ['nome'=>'Fernando López Mozos',                'foto'=>'fernando-lopes'],
            ['nome'=>'Filipe Barcelos',                      'foto'=>'filipe-barcelos'],
            ['nome'=>'Francisco Eduardo Silva',             'foto'=>'francisco-eduardo'],
            ['nome'=>'Frederico Michelino',                 'foto'=>'frederico-michelino'],
            ['nome'=>'Guilherme Biazotto',                  'foto'=>'guilherme-biazotto'],
            ['nome'=>'Gustavo de Mendonça Borges',          'foto'=>'gustavo-mendonca'],
            ['nome'=>'Gustavo P. Fraga',                    'foto'=>'gustavo-fraga'],
            ['nome'=>'Henrique José V. Silveira',           'foto'=>'henrique-jose'],
            ['nome'=>'Henrique Valério de Mesquita',        'foto'=>'henrique-valerio'],
            ['nome'=>'Jackson Vinícius de Lima Bertuol',    'foto'=>'jackson-vinicius'],
            ['nome'=>'José Alberto Fernandes da Silva Filho','foto'=>'jose-alberto'],
            ['nome'=>'José Aurélio Ramalho',                'foto'=>'jose-aurelio'],
            ['nome'=>'José Benedito Bortoto',               'foto'=>'jose-benedito-bortoto'],
            ['nome'=>'Larissa Berbert',                     'foto'=>'larissa-berbert'],
            ['nome'=>'Lucas Fileni',                        'foto'=>'lucas-fileni'],
            ['nome'=>'Luis Teodoro da Luz',                 'foto'=>'luis-teodoro'],
            ['nome'=>'Nara Gelle de Oliveira',              'foto'=>'nara-gelle'],
            ['nome'=>'Phillipe Abreu',                      'foto'=>'phillipe-abreu'],
            ['nome'=>'Dr. Renato Diniz Lins',               'foto'=>'renato-diniz'],
            ['nome'=>'Rodrigo Barros de Carvalho',          'foto'=>'rodrigo-barros'],
            ['nome'=>'Rodrigo Caselli Belém',               'foto'=>'rodrigo-caselli'],
            ['nome'=>'Romeo L. Simões',                     'foto'=>'romeo-simoes'],
            ['nome'=>'Prof. Dr. Santiago Servin',           'foto'=>'santiago-servin'],
            ['nome'=>'Saulo Ferreira',                      'foto'=>'saulo-ferreira'],
            ['nome'=>'Thiago R. Calderan',                  'foto'=>'thiago-calderan'],
            ['nome'=>'Tiago Leal Ghezzi',                   'foto'=>'tiago-leal'],
            ['nome'=>'Vinicius Sampaio',                    'foto'=>'vinicius-sampaio'],
            ['nome'=>'Vitor F. Kruger',                     'foto'=>'vitor-kruger'],
            ['nome'=>'Waldemar Prandi Filho',               'foto'=>'waldemar-prandi'],
            ['nome'=>'Wellington José dos Santos',          'foto'=>'wellington-santos'],
            ['nome'=>'Willian G. Hashimoto H. de Sousa',   'foto'=>'willian-hashimoto'],
          ];
          foreach ($apoiadores as $ap): ?>
          <div class="sup-slide">
            <div class="sup-img-wrap">
              <img src="assets/img/apoiadores/<?= h($ap['foto']) ?>.png?v=<?= ASSET_VERSION ?>" alt="<?= h($ap['nome']) ?>" loading="lazy" onerror="this.style.opacity='0'">
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <button class="sup-carousel-btn index-carousel-btn" type="button" data-carousel-next aria-label="Próximo">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
      </button>
      <div class="sup-dots index-carousel-dots" id="supDots" data-carousel-dots></div>
    </div>
  </div>
</section>

<style>
/* ── Carrossel Apoiadores ─────────────────────────────────── */
.sup-carousel-outer {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.sup-viewport {
  flex: 1;
  overflow: hidden;
  border-radius: var(--radius-lg);
  mask-image: linear-gradient(to right, transparent 0%, #000 8%, #000 92%, transparent 100%);
  -webkit-mask-image: linear-gradient(to right, transparent 0%, #000 8%, #000 92%, transparent 100%);
}
.sup-track {
  display: flex;
  gap: 0.85rem;
  /* scroll via JS — sem animação CSS */
}
.sup-slide {
  flex: 0 0 150px;
  border-radius: var(--radius-lg);
  overflow: hidden;
  box-shadow: var(--shadow-sm);
  transition: transform 0.22s ease, box-shadow 0.22s ease;
  cursor: default;
}
.sup-slide:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); }
.sup-img-wrap {
  width: 100%;
  aspect-ratio: 9 / 16;
  background: linear-gradient(135deg, var(--slate-100), var(--slate-200));
  overflow: hidden;
}
.sup-img-wrap img {
  width: 100%; height: 100%;
  object-fit: cover;
  object-position: center;
  display: block;
}
.sup-carousel-btn {
  flex-shrink: 0;
  width: 36px; height: 36px;
  border-radius: 50%;
  background: var(--white);
  border: 1px solid var(--slate-200);
  color: var(--navy);
  display: flex; align-items: center; justify-content: center;
  cursor: pointer;
  box-shadow: var(--shadow-sm);
  transition: background 0.18s, color 0.18s, transform 0.15s;
}
.sup-carousel-btn:hover { background: var(--navy); color: #fff; transform: scale(1.08); }
.sup-dots {
  display: flex; justify-content: center;
  gap: 0.45rem; margin-top: 0.9rem;
}
.sup-dot {
  width: 7px; height: 7px;
  border-radius: 50%;
  background: var(--slate-300);
  border: none; padding: 0;
  cursor: pointer;
  transition: background 0.2s, transform 0.2s;
}
.sup-dot.active { background: var(--navy); transform: scale(1.45); }

/* ── Carrossel padrão do index ───────────────────────────── */
.index-carousel {
  display: grid;
  grid-template-columns: auto minmax(0, 1fr) auto;
  align-items: center;
  gap: 0.6rem;
}
.index-carousel-track {
  display: flex;
  gap: 1rem;
  overflow-x: auto;
  overscroll-behavior-inline: contain;
  scroll-snap-type: x mandatory;
  scroll-padding-inline: 2px;
  padding: 0.2rem 0.1rem 0.75rem;
  scrollbar-width: thin;
}
.index-carousel-btn {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: 1px solid var(--slate-200);
  background: var(--white);
  color: var(--navy);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: var(--shadow-sm);
  transition: background 0.18s, color 0.18s, transform 0.15s;
}
.index-carousel-btn:hover {
  background: var(--navy);
  color: #fff;
  transform: scale(1.06);
}
.index-carousel-dots {
  grid-column: 1 / -1;
  display: flex;
  justify-content: center;
  gap: 0.45rem;
}
.index-carousel-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: var(--slate-300);
  border: 0;
  padding: 0;
  cursor: pointer;
  transition: background 0.2s, transform 0.2s, width 0.2s;
}
.index-carousel-dot.active {
  width: 18px;
  border-radius: 99px;
  background: var(--navy);
}
.post-card {
  flex: 0 0 min(330px, 84vw);
  scroll-snap-align: start;
  background: var(--white);
  border: 1px solid var(--slate-200);
  border-radius: var(--radius-lg);
  overflow: hidden;
  color: inherit;
  text-decoration: none;
  box-shadow: var(--shadow-sm);
  transition: transform var(--transition), box-shadow var(--transition), border-color var(--transition);
}
.post-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
  border-color: #BAE6FD;
}
.post-card-media {
  aspect-ratio: 1 / 1;
  background: linear-gradient(135deg, #E0F2FE, #F8FAFC);
  overflow: hidden;
  display: grid;
  place-items: center;
}
.post-card-media img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}
.post-card-placeholder {
  color: var(--navy);
  font-size: 2.1rem;
}
.post-card-body {
  padding: 1rem;
  display: flex;
  flex-direction: column;
  min-height: 210px;
}
.post-card h3 {
  color: var(--navy);
  font-family: var(--font-display);
  font-size: 1rem;
  line-height: 1.25;
  margin: 0 0 0.45rem;
}
.post-card p {
  color: var(--slate-600);
  font-size: 0.8rem;
  line-height: 1.55;
  margin: 0 0 0.8rem;
}
.post-source-pill {
  display: inline-flex;
  align-self: flex-start;
  border-radius: var(--radius-full);
  background: #E0F2FE;
  color: var(--navy);
  border: 1px solid #BAE6FD;
  padding: 3px 9px;
  font-size: 0.62rem;
  font-weight: 800;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  margin-bottom: 0.7rem;
}

/* ── Programas em destaque ───────────────────────────────── */
.programs-feature {
  margin: 0 0 2rem;
  padding: 1.15rem;
  border: 1px solid var(--slate-200);
  border-radius: var(--radius-lg);
  background: linear-gradient(135deg, #fff 0%, #F8FAFC 100%);
  box-shadow: var(--shadow-sm);
}
.programs-feature-head {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 1rem;
  margin-bottom: 1rem;
}
.programs-feature-head h3 {
  font-family: var(--font-display);
  color: var(--navy);
  font-size: 1.05rem;
  line-height: 1.2;
  margin: 0.15rem 0 0;
}
.programs-feature-head p {
  color: var(--slate-600);
  font-size: 0.78rem;
  line-height: 1.55;
  margin: 0.25rem 0 0;
}
.programs-feature-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1rem;
}
.programs-carousel {
  display: grid;
  grid-template-columns: auto minmax(0, 1fr) auto;
  align-items: center;
  gap: 0.6rem;
}
.programs-carousel-track {
  display: flex;
  gap: 1rem;
  overflow-x: auto;
  overscroll-behavior-inline: contain;
  scroll-snap-type: x mandatory;
  scroll-padding-inline: 2px;
  padding: 0.2rem 0.1rem 0.75rem;
  scrollbar-width: thin;
}
.programs-carousel-track .program-card {
  flex: 0 0 min(360px, 84vw);
  scroll-snap-align: start;
}
.programs-carousel-btn {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  border: 1px solid var(--slate-200);
  background: var(--white);
  color: var(--navy);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: var(--shadow-sm);
  transition: background 0.18s, color 0.18s, transform 0.15s;
}
.programs-carousel-btn:hover {
  background: var(--navy);
  color: #fff;
  transform: scale(1.06);
}
.program-card {
  --program-accent: var(--sky);
  --program-accent-dark: var(--navy);
  display: grid;
  grid-template-columns: 122px minmax(0, 1fr);
  min-height: 210px;
  border: 1px solid var(--slate-200);
  border: 1px solid color-mix(in srgb, var(--program-accent) 36%, var(--slate-200));
  border-radius: var(--radius-lg);
  background: var(--white);
  color: inherit;
  text-decoration: none;
  overflow: hidden;
  position: relative;
  transition: transform var(--transition), box-shadow var(--transition), border-color var(--transition);
}
.program-card::before {
  content: '';
  position: absolute;
  inset: 0 0 auto 0;
  height: 4px;
  background: linear-gradient(90deg, var(--program-accent), var(--program-accent-dark));
}
.program-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
  border-color: var(--program-accent);
  border-color: color-mix(in srgb, var(--program-accent) 70%, var(--slate-200));
}
.program-card--salvando { --program-accent: #DC2626; --program-accent-dark: #013684; }
.program-card--party { --program-accent: #22C55E; --program-accent-dark: #0EA5E9; }
.program-card--laranja { --program-accent: #F97316; --program-accent-dark: #B45309; }
.program-card-media {
  background: linear-gradient(135deg, #E0F2FE, #F8FAFC);
  overflow: hidden;
}
.program-card-media img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: top center;
  display: block;
}
.program-card-body {
  padding: 1.15rem;
  display: flex;
  flex-direction: column;
  min-width: 0;
}
.program-badge {
  display: inline-flex;
  align-self: flex-start;
  background: #E0F2FE;
  background: color-mix(in srgb, var(--program-accent) 14%, #fff);
  color: var(--program-accent-dark);
  border: 1px solid var(--slate-200);
  border: 1px solid color-mix(in srgb, var(--program-accent) 35%, #fff);
  border-radius: var(--radius-full);
  padding: 3px 9px;
  font-size: 0.62rem;
  font-weight: 800;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  margin-bottom: 0.7rem;
}
.program-card h4 {
  font-family: var(--font-display);
  color: var(--navy);
  font-size: 1rem;
  line-height: 1.22;
  margin: 0 0 0.45rem;
}
.program-card p {
  color: var(--slate-600);
  font-size: 0.78rem;
  line-height: 1.55;
  margin: 0 0 0.8rem;
}
.program-card-link {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  color: var(--program-accent-dark);
  font-weight: 700;
  font-size: 0.76rem;
  margin-top: auto;
}
@media (max-width: 640px) {
  .sup-slide { flex: 0 0 120px; }
  .sup-carousel-btn { width: 30px; height: 30px; }
  .programs-feature { padding: 1rem; }
  .programs-feature-head { align-items: flex-start; flex-direction: column; }
  .programs-feature-grid { grid-template-columns: 1fr; }
  .index-carousel { grid-template-columns: 1fr; }
  .index-carousel-track { padding-inline: 0; }
  .index-carousel-btn { display: none; }
  .programs-carousel { grid-template-columns: 1fr; }
  .programs-carousel-btn { display: none; }
  .program-card { grid-template-columns: 104px minmax(0, 1fr); min-height: 190px; }
  .program-card-body { padding: 1rem; }
}
@media (max-width: 400px) {
  .sup-slide { flex: 0 0 100px; }
  .program-card { grid-template-columns: 1fr; }
  .program-card-media { aspect-ratio: 16 / 9; }
}
</style>

<script>
(function () {
  document.querySelectorAll('[data-index-carousel]').forEach(carousel => {
    const track = carousel.querySelector('[data-carousel-track]');
    const prev = carousel.querySelector('[data-carousel-prev]');
    const next = carousel.querySelector('[data-carousel-next]');
    const dotsWrap = carousel.querySelector('[data-carousel-dots]');
    if (!track || !prev || !next || !dotsWrap) return;

    const stepCount = Math.max(1, parseInt(carousel.dataset.carouselStep || '1', 10));
    const autoplay = parseInt(carousel.dataset.carouselAutoplay || '0', 10);
    let paused = false;

    function items() {
      return Array.from(track.children).filter(el => el.offsetParent !== null);
    }

    function cardStep() {
      const item = items()[0];
      if (!item) return Math.max(280, track.clientWidth * 0.75);
      const styles = getComputedStyle(track);
      const gap = parseFloat(styles.columnGap || styles.gap || '16') || 16;
      return item.getBoundingClientRect().width + gap;
    }

    function maxIndex() {
      return Math.max(0, Math.ceil((track.scrollWidth - track.clientWidth) / cardStep()));
    }

    function currentIndex() {
      return Math.max(0, Math.round(track.scrollLeft / cardStep()));
    }

    function goTo(index) {
      const bounded = Math.max(0, Math.min(index, maxIndex()));
      track.scrollTo({ left: bounded * cardStep(), behavior: 'smooth' });
    }

    function syncDots() {
      const max = maxIndex();
      const total = Math.min(7, max + 1);
      if (dotsWrap.children.length !== total) {
        dotsWrap.innerHTML = '';
        for (let i = 0; i < total; i++) {
          const dot = document.createElement('button');
          dot.className = 'index-carousel-dot';
          dot.type = 'button';
          dot.setAttribute('aria-label', 'Página ' + (i + 1));
          dot.addEventListener('click', () => {
            goTo(Math.round(i * max / Math.max(1, total - 1)));
          });
          dotsWrap.appendChild(dot);
        }
      }
      const active = max > 0 ? Math.round((currentIndex() / max) * (total - 1)) : 0;
      Array.from(dotsWrap.children).forEach((dot, i) => dot.classList.toggle('active', i === active));
      const isScrollable = track.scrollWidth > track.clientWidth + 2;
      prev.style.visibility = isScrollable ? 'visible' : 'hidden';
      next.style.visibility = isScrollable ? 'visible' : 'hidden';
      dotsWrap.style.display = isScrollable ? 'flex' : 'none';
    }

    prev.addEventListener('click', () => goTo(currentIndex() - stepCount));
    next.addEventListener('click', () => {
      const nextIndex = currentIndex() + stepCount;
      goTo(nextIndex > maxIndex() ? 0 : nextIndex);
    });
    track.addEventListener('scroll', () => window.requestAnimationFrame(syncDots), { passive: true });
    carousel.addEventListener('mouseenter', () => { paused = true; });
    carousel.addEventListener('mouseleave', () => { paused = false; });
    window.addEventListener('resize', syncDots, { passive: true });

    if (autoplay > 0) {
      setInterval(() => {
        if (!paused && track.scrollWidth > track.clientWidth + 2) {
          const nextIndex = currentIndex() + stepCount;
          goTo(nextIndex > maxIndex() ? 0 : nextIndex);
        }
      }, autoplay);
    }

    syncDots();
  });
})();
</script>

<!-- POSTS -->
<section class="section" id="posts" aria-labelledby="posts-title">
  <div class="section-inner">
    <div class="section-header" data-animate>
      <div class="divider" aria-hidden="true"></div>
      <span class="section-label">Instagram e publicações</span>
      <h2 class="section-title" id="posts-title">
        <a href="pages/noticias.php" class="section-title-link">Posts</a>
        <a href="<?= INSTAGRAM_URL ?>" class="section-page-link" target="_blank" rel="noopener noreferrer" aria-label="Abrir Instagram do CoBraLT">ver Instagram &rarr;</a>
      </h2>
      <p class="section-subtitle">Acompanhe as publicações mais recentes do CoBraLT. Quando o token do Instagram estiver configurado, esta área sincroniza automaticamente com o perfil oficial.</p>
    </div>

    <?php if (empty($homePosts)): ?>
      <p style="color:var(--slate-400);text-align:center;">Nenhum post publicado ainda.</p>
    <?php else: ?>
    <div class="index-carousel" data-index-carousel data-carousel-step="1">
      <button class="index-carousel-btn" type="button" data-carousel-prev aria-label="Post anterior">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M15 18l-6-6 6-6"/></svg>
      </button>

      <div class="index-carousel-track" data-carousel-track>
        <?php foreach ($homePosts as $i => $post):
          $cat      = h($post['category'] ?? 'Post');
          $dtRaw    = (string)($post['published_at'] ?? '');
          $dt       = $dtRaw !== '' ? fmtDate($dtRaw) : '';
          $dtIso    = $dtRaw !== '' ? substr($dtRaw, 0, 10) : '';
          $url      = (string)($post['url'] ?? '#');
          $external = !empty($post['external']);
          $target   = $external ? ' target="_blank" rel="noopener noreferrer"' : '';
          $image    = trim((string)($post['image'] ?? ''));
          $source   = ($post['source'] ?? '') === 'instagram' ? 'Instagram' : 'Post';
        ?>
        <a href="<?= h($url) ?>" class="post-card" data-animate data-animate-delay="<?= ($i % 3) + 1 ?>"<?= $target ?>>
          <div class="post-card-media">
            <?php if ($image !== ''): ?>
              <img src="<?= h($image) ?>" alt="<?= h($post['title'] ?? 'Post do CoBraLT') ?>" loading="lazy">
            <?php else: ?>
              <span class="post-card-placeholder" aria-hidden="true">#</span>
            <?php endif; ?>
          </div>
          <div class="post-card-body">
            <span class="post-source-pill"><?= h($source) ?> · <?= $cat ?></span>
            <div class="news-meta">
              <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              <time datetime="<?= h($dtIso) ?>"><?= h($dt) ?></time>
            </div>
            <h3><?= h($post['title'] ?? '') ?></h3>
            <p><?= h($post['excerpt'] ?? '') ?></p>
            <span class="news-link">
              <?= $external ? 'Ver no Instagram' : 'Ler post' ?>
              <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
            </span>
          </div>
        </a>
        <?php endforeach; ?>
      </div>

      <button class="index-carousel-btn" type="button" data-carousel-next aria-label="Próximo post">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>
      </button>
      <div class="index-carousel-dots" data-carousel-dots></div>
    </div>
    <?php endif; ?>
  </div>
</section>
<!-- ═══ PROJETOS ══════════════════════════════════════════ -->
<section class="section" id="boletimes" aria-labelledby="boletimes-title">
  <div class="section-inner">
    <div class="section-header" data-animate>
      <div class="divider" aria-hidden="true"></div>
      <span class="section-label">Boletim CoBraLT</span>
      <h2 class="section-title" id="boletimes-title">
        <a href="pages/boletimes.php" class="section-title-link">Trauma BoleTIME</a>
        <a href="pages/boletimes.php" class="section-page-link">ver edições →</a>
      </h2>
      <p class="section-subtitle">Publicação trimestral do CoBraLT com notícias, entrevistas, ações das ligas, agenda científica e atualizações sobre trauma no Brasil.</p>
    </div>

    <div class="boletime-feature" data-animate>
      <div class="boletime-feature-head">
        <div>
          <span class="section-label">Edições trimestrais</span>
          <h3>Informativo oficial das Ligas do Trauma</h3>
          <p>Leia online, abra em tela cheia ou baixe cada edição do boletim institucional do CoBraLT.</p>
        </div>
        <a href="pages/boletimes.php" class="news-link" style="font-size:0.82rem;">Ver boletins →</a>
      </div>

      <div class="boletime-grid">
        <a href="pages/boletime-maio-2026.php" class="boletime-card" aria-label="Abrir edição Maio 2026 do Trauma BoleTIME">
          <div class="boletime-card-media">
            <img src="assets/img/boletimes/trauma-boletime-maio-2026-cover.png" alt="Capa da edição Maio 2026 do Trauma BoleTIME" loading="lazy">
          </div>
          <div class="boletime-card-body">
            <span class="boletime-badge">Edição 1 · 2º trimestre/2026</span>
            <h4>Trauma BoleTIME - Maio 2026</h4>
            <p>Edição inaugural com os 23 anos do CoBraLT, entrevista com o Prof. Dr. Gustavo Pereira Fraga, ações das ligas, CoLT/COTREM, ECTES e agenda de eventos.</p>
            <span class="boletime-card-link">Ver edição <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
          </div>
        </a>
      </div>
    </div>
  </div>
</section>

<section class="section" id="projetos" aria-labelledby="projetos-title">
  <div class="section-inner">
    <div class="section-header" data-animate>
      <div class="divider" aria-hidden="true"></div>
      <span class="section-label">Iniciativas</span>
      <h2 class="section-title" id="projetos-title">
        <a href="pages/projetos.php" class="section-title-link">Projetos</a>
        <a href="pages/projetos.php" class="section-page-link">ver página →</a>
      </h2>
      <p class="section-subtitle">Conheça os programas e projetos oficiais do CoBraLT para ligas filiadas.</p>
    </div>

    <div class="programs-feature" data-animate>
      <div class="programs-feature-head">
        <div>
          <span class="section-label">Programas e materiais oficiais</span>
          <h3>Editais, publicações e adesões</h3>
          <p>Documentos de referência para ligas filiadas, com leitura online, tela cheia e download dos PDFs.</p>
        </div>
        <a href="pages/projetos.php" class="news-link" style="font-size:0.82rem;">Ver projetos →</a>
      </div>

      <div class="programs-carousel index-carousel" data-index-carousel data-carousel-step="1">
        <button class="programs-carousel-btn index-carousel-btn" type="button" data-carousel-prev aria-label="Anterior">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <div class="programs-feature-grid programs-carousel-track index-carousel-track" data-carousel-track>
        <a href="pages/programa-salvando-vidas-2026.php" class="program-card program-card--salvando" aria-label="Abrir página do Programa Salvando Vidas 2026">
          <div class="program-card-media">
            <img src="assets/img/programas/salvando-vidas-2026-cover.png" alt="Capa do edital do Programa Salvando Vidas 2026" loading="lazy">
          </div>
          <div class="program-card-body">
            <span class="program-badge">Maio-dez/2026</span>
            <h4>Programa Salvando Vidas 2026</h4>
            <p>Projeto social de prevenção, primeiros socorros e educação em urgência, emergência e trauma.</p>
            <span class="program-card-link">Ver edital e materiais <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
          </div>
        </a>

        <a href="pages/programa-salvando-vidas-controle-hemorragias.php" class="program-card program-card--salvando" aria-label="Abrir material Controle de Hemorragias do Programa Salvando Vidas">
          <div class="program-card-media">
            <img src="assets/img/programas/salvando-vidas-controle-hemorragias-cover.png" alt="Capa do material Controle de Hemorragias" loading="lazy">
          </div>
          <div class="program-card-body">
            <span class="program-badge">34 páginas · PDF</span>
            <h4>Salvando Vidas - Controle de Hemorragias</h4>
            <p>Publicação sobre trauma, xABCDE, pressão direta, torniquetes, hemostáticos, Stop the Bleed e prevenção.</p>
            <span class="program-card-link">Ver material <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
          </div>
        </a>

        <a href="pages/programa-salvando-vidas-anafilaxia.php" class="program-card program-card--salvando" aria-label="Abrir material Reação Alérgica Grave e Anafilaxia do Programa Salvando Vidas">
          <div class="program-card-media">
            <img src="assets/img/programas/salvando-vidas-anafilaxia-cover.png" alt="Capa do material Reação Alérgica Grave e Anafilaxia" loading="lazy">
          </div>
          <div class="program-card-body">
            <span class="program-badge">37 páginas · PDF</span>
            <h4>Salvando Vidas - Anafilaxia</h4>
            <p>Publicação sobre alergias graves, anafilaxia, sinais de gravidade, tratamento, urticária e angioedema.</p>
            <span class="program-card-link">Ver material <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
          </div>
        </a>

        <a href="pages/programa-salvando-vidas-avc-iam.php" class="program-card program-card--salvando" aria-label="Abrir material AVC e IAM do Programa Salvando Vidas">
          <div class="program-card-media">
            <img src="assets/img/programas/salvando-vidas-avc-iam-cover.png" alt="Capa do material AVC e IAM" loading="lazy">
          </div>
          <div class="program-card-body">
            <span class="program-badge">44 páginas · PDF</span>
            <h4>Salvando Vidas - AVC e IAM</h4>
            <p>Publicação sobre reconhecimento precoce, sinais de alerta e orientações iniciais para AVC e infarto agudo do miocárdio.</p>
            <span class="program-card-link">Ver material <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
          </div>
        </a>

        <a href="pages/programa-salvando-vidas-queimaduras-choques-eletricos.php" class="program-card program-card--salvando" aria-label="Abrir material Queimaduras e Choques Elétricos do Programa Salvando Vidas">
          <div class="program-card-media">
            <img src="assets/img/programas/salvando-vidas-queimaduras-choques-eletricos-cover.png" alt="Capa do material Queimaduras e Choques Elétricos" loading="lazy">
          </div>
          <div class="program-card-body">
            <span class="program-badge">48 páginas · PDF</span>
            <h4>Salvando Vidas - Queimaduras e Choques Elétricos</h4>
            <p>Publicação sobre prevenção, classificação, cuidados iniciais e segurança em queimaduras e acidentes elétricos.</p>
            <span class="program-card-link">Ver material <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
          </div>
        </a>
        <a href="pages/programa-party-brasil-2026.php" class="program-card program-card--party" aria-label="Abrir página do Programa P.A.R.T.Y. Brasil 2026">
          <div class="program-card-media">
            <img src="assets/img/programas/party-brasil-2026-cover.png" alt="Capa do edital do Programa P.A.R.T.Y. Brasil 2026" loading="lazy">
          </div>
          <div class="program-card-body">
            <span class="program-badge">Edital 10/04/2026</span>
            <h4>Programa P.A.R.T.Y. Brasil 2026</h4>
            <p>Estratégia de prevenção primária do trauma em jovens, com edital de adesão e ficha de inscrição.</p>
            <span class="program-card-link">Ver documentos <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
          </div>
        </a>

        <a href="pages/programa-junho-laranja-2026.php" class="program-card program-card--laranja" aria-label="Abrir página do programa Junho Laranja 2026">
          <div class="program-card-media">
            <img src="assets/img/programas/junho-laranja-2026-cover.png" alt="Capa do documento Prevenção e Extensão - Junho Laranja 2026" loading="lazy">
          </div>
          <div class="program-card-body">
            <span class="program-badge">Junho Laranja</span>
            <h4>Prevenção e Extensão - Junho Laranja 2026</h4>
            <p>Campanha de prevenção de queimaduras e acidentes ocupacionais com ações educativas e extensionistas.</p>
            <span class="program-card-link">Ver documento <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></span>
          </div>
        </a>
        </div>
        <button class="programs-carousel-btn index-carousel-btn" type="button" data-carousel-next aria-label="Próximo">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
        <div class="index-carousel-dots" data-carousel-dots></div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ EVENTOS ═══════════════════════════════════════════ -->
<section class="section leagues-section" id="eventos" aria-labelledby="eventos-title">
  <div class="section-inner">
    <div class="section-header" data-animate>
      <div class="divider" aria-hidden="true"></div>
      <span class="section-label">Agenda</span>
      <h2 class="section-title" id="eventos-title">
        <a href="pages/eventos.php" class="section-title-link">Eventos e Congressos</a>
        <a href="pages/eventos.php" class="section-page-link">ver página →</a>
      </h2>
      <p class="section-subtitle">Mantenha-se atualizado com a agenda de eventos e congressos do CoBraLT.</p>
    </div>

    <div class="events-grid">
      <?php if (empty($eventos)): ?>
        <p style="color:var(--slate-400);grid-column:1/-1;text-align:center;">Nenhum evento cadastrado.</p>
      <?php else: foreach ($eventos as $i => $ev):
        $extAttr = $ev['link_externo'] ? 'target="_blank" rel="noopener noreferrer"' : '';
        $diaStyle = mb_strlen($ev['dia']) > 2 ? 'font-size:1rem;line-height:1.1;' : '';
      ?>
      <div class="event-card" data-animate data-animate-delay="<?= $i ?>">
        <div class="event-header">
          <div class="event-date-box">
            <div class="event-day" style="<?= $diaStyle ?>"><?= h($ev['dia']) ?></div>
            <div class="event-month"><?= h($ev['mes']) ?></div>
          </div>
          <h3 class="event-title"><?= h($ev['titulo']) ?></h3>
        </div>
        <div class="event-body">
          <div class="event-location">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            <?= h($ev['local']) ?>
          </div>
          <p class="event-desc"><?= h($ev['descricao']) ?></p>
          <a href="<?= h($ev['link']) ?>" class="news-link" <?= $extAttr ?>><?= h($ev['link_texto']) ?></a>
        </div>
      </div>
      <?php endforeach; endif; ?>
    </div>

    <!-- Realizados preview -->
    <div style="margin-top:2.5rem;padding-top:2rem;border-top:1px solid var(--slate-200);" data-animate>
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1.25rem;flex-wrap:wrap;gap:0.5rem;">
        <div>
          <span style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:0.1em;color:var(--slate-400);">Memória</span>
          <h3 style="font-family:var(--font-display);color:var(--navy);font-size:1rem;margin:0.15rem 0 0;">Eventos Realizados</h3>
        </div>
        <a href="pages/eventos.php#realizados" class="news-link" style="font-size:0.82rem;">Ver todos →</a>
      </div>
      <div style="display:flex;gap:1rem;overflow-x:auto;scrollbar-width:none;padding-bottom:0.25rem;">
        <?php foreach ($homePastColts as $colt):
          $title = trim($colt['edition'] . ($colt['year'] ? ' ' . $colt['year'] : ''));
          $placeLine = trim(($colt['place'] ?: 'Brasil') . ($colt['year'] ? ', ' . $colt['year'] : ''));
        ?>
        <a href="pages/<?= h($colt['file']) ?>" class="colt2012-card" style="flex:0 0 220px;">
          <span style="font-size:0.63rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;background:linear-gradient(135deg,var(--navy),var(--sky-dark));color:#fff;padding:2px 8px;border-radius:99px;width:fit-content;"><?= h($colt['badge']) ?></span>
          <div style="font-size:1.5rem;margin:0.2rem 0;">🏆</div>
          <div style="font-size:0.68rem;font-weight:700;color:var(--sky-dark);text-transform:uppercase;letter-spacing:0.1em;"><?= h($colt['edition']) ?></div>
          <div style="font-family:var(--font-display);color:var(--navy);font-size:0.92rem;font-weight:800;line-height:1.2;"><?= h($title) ?></div>
          <div style="color:var(--slate-400);font-size:0.72rem;"><?= h($placeLine) ?></div>
          <div style="color:var(--slate-600);font-size:0.7rem;margin-top:0.25rem;">Acervo histórico do CoBraLT</div>
          <span style="font-size:0.75rem;font-weight:600;color:var(--sky-dark);margin-top:0.5rem;">Ver detalhes →</span>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- ═══ FILIAÇÃO ══════════════════════════════════════════ -->
<section class="section" id="filiacao" aria-labelledby="filiacao-title">
  <div class="section-inner" style="max-width:960px;">
    <div class="section-header centered" data-animate>
      <div class="divider" style="margin:0 auto 1rem;" aria-hidden="true"></div>
      <span class="section-label">Integre a rede</span>
      <h2 class="section-title" id="filiacao-title">Filiar sua Liga ao CoBraLT</h2>
      <p class="section-subtitle">Conecte sua instituição à maior rede de trauma do Brasil.</p>
    </div>
    <div class="filiacao-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;margin-bottom:2.25rem;">
      <div class="contact-form" style="background:var(--off-white);" data-animate-left data-animate-delay="1">
        <h3 style="font-family:var(--font-display);color:var(--navy);margin-bottom:1.1rem;font-size:1.1rem;">O que sua liga ganha</h3>
        <div style="display:flex;flex-direction:column;gap:0.6rem;">
          <div style="display:flex;align-items:flex-start;gap:0.75rem;">
            <span style="width:28px;height:28px;background:linear-gradient(135deg,var(--sky),var(--navy));border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:0.72rem;">🏥</span>
            <div><div style="font-weight:600;color:var(--navy);font-size:0.84rem;">Programas Clínicos Exclusivos</div><div style="color:var(--slate-600);font-size:0.78rem;line-height:1.5;">Acesso ao Salvando Vidas, P.A.R.T.Y e Conecta Trauma</div></div>
          </div>
          <div style="display:flex;align-items:flex-start;gap:0.75rem;">
            <span style="width:28px;height:28px;background:linear-gradient(135deg,var(--sky),var(--navy));border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:0.72rem;">🎓</span>
            <div><div style="font-weight:600;color:var(--navy);font-size:0.84rem;">Descontos em Congressos</div><div style="color:var(--slate-600);font-size:0.78rem;line-height:1.5;">Condições especiais em CoLTs, eventos parceiros e atividades futuras</div></div>
          </div>
          <div style="display:flex;align-items:flex-start;gap:0.75rem;">
            <span style="width:28px;height:28px;background:linear-gradient(135deg,var(--sky),var(--navy));border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:0.72rem;">🔬</span>
            <div><div style="font-weight:600;color:var(--navy);font-size:0.84rem;">Ciência & Pesquisa Nacional</div><div style="color:var(--slate-600);font-size:0.78rem;line-height:1.5;">Participação em projetos científicos e apoio institucional a publicações</div></div>
          </div>
          <div style="display:flex;align-items:flex-start;gap:0.75rem;">
            <span style="width:28px;height:28px;background:linear-gradient(135deg,var(--sky),var(--navy));border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:0.72rem;">🤝</span>
            <div><div style="font-weight:600;color:var(--navy);font-size:0.84rem;">Rede Interinstitucional</div><div style="color:var(--slate-600);font-size:0.78rem;line-height:1.5;">Integração com ligas e orientadores de todo o Brasil</div></div>
          </div>
          <div style="display:flex;align-items:flex-start;gap:0.75rem;">
            <span style="width:28px;height:28px;background:linear-gradient(135deg,var(--sky),var(--navy));border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:0.72rem;">📋</span>
            <div><div style="font-weight:600;color:var(--navy);font-size:0.84rem;">Consultoria & Materiais</div><div style="color:var(--slate-600);font-size:0.78rem;line-height:1.5;">Suporte para abertura de ligas, cartilhas revisadas e apoio institucional</div></div>
          </div>
        </div>
        <a href="pages/ligas.php#beneficios" style="display:inline-flex;align-items:center;gap:5px;margin-top:1rem;font-size:0.78rem;font-weight:600;color:var(--sky-dark);text-decoration:none;">Ver todos os benefícios →</a>
      </div>
      <div class="contact-form" style="display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;gap:1.1rem;" data-animate-right data-animate-delay="1">
        <div style="width:58px;height:58px;background:rgba(0,43,78,0.08);border-radius:50%;display:flex;align-items:center;justify-content:center;">
          <svg width="27" height="27" fill="none" stroke="var(--navy)" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <div>
          <div style="font-size:0.76rem;font-weight:700;color:var(--navy);text-transform:uppercase;letter-spacing:0.08em;">Filiação 2026</div>
          <p style="color:var(--slate-600);margin-top:0.5rem;line-height:1.7;font-size:0.93rem;">Preencha o formulário oficial de filiação para conectar sua liga à nossa rede.</p>
        </div>
        <a href="https://docs.google.com/forms/d/e/1FAIpQLSdxlJya6NcxHC1H_Cuao0mtN5_UehL-6RSzuhzj2JlEBtI9qA/viewform" target="_blank" rel="noopener noreferrer" class="btn btn-affiliate">
          <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
          Acessar formulário de filiação
        </a>
        <p style="font-size:0.76rem;color:var(--slate-400);">Você será redirecionado para o Google Forms.</p>
      </div>
    </div>
  </div>
</section>

<!-- ═══ LIGAS ══════════════════════════════════════════════ -->
<section class="section leagues-section" id="ligas" aria-labelledby="ligas-title">
  <div class="section-inner">
    <div class="section-header centered" data-animate>
      <div class="divider" style="margin:0 auto 1rem;" aria-hidden="true"></div>
      <span class="section-label">Rede Nacional</span>
      <h2 class="section-title" id="ligas-title">
        <a href="pages/ligas.php" class="section-title-link">Ligas Afiliadas</a>
        <a href="pages/ligas.php" class="section-page-link">ver página →</a>
      </h2>
      <p class="section-subtitle">180+ instituições organizadas em 6 ligas regionais, cobrindo todo o território nacional.</p>
    </div>
    <div class="leagues-grid">
      <?php if (empty($ligas)): ?>
        <p style="color:var(--slate-400);grid-column:1/-1;text-align:center;">Nenhuma liga cadastrada.</p>
      <?php else: foreach ($ligas as $i => $liga): ?>
      <div class="league-card" data-animate data-animate-delay="<?= $i ?>">
        <div class="league-icon" aria-hidden="true"><?= h($liga['emoji']) ?></div>
        <h3><?= h($liga['nome']) ?></h3>
        <p><?= h($liga['descricao']) ?></p>
        <span class="league-count"><?= h($liga['contagem']) ?></span>
        <a href="<?= h($liga['link']) ?>" class="news-link" style="margin-top:.75rem;display:inline-flex;">Ver ligas →</a>
      </div>
      <?php endforeach; endif; ?>
    </div>
  </div>
</section>

<!-- ═══ HISTÓRIA ══════════════════════════════════════════ -->
<section class="section" id="historia" aria-labelledby="historia-title">
  <div class="section-inner">
    <div class="section-header" data-animate>
      <div class="divider" aria-hidden="true"></div>
      <span class="section-label">Trajetória</span>
      <h2 class="section-title" id="historia-title">
        <a href="pages/historia.php" class="section-title-link">Nossa História</a>
        <a href="pages/historia.php" class="section-page-link">ver página →</a>
      </h2>
      <p class="section-subtitle">De 2003 ao presente — construindo a maior rede acadêmica de trauma do Brasil.</p>
    </div>
    <div class="timeline" role="list">
      <div class="timeline-item" role="listitem" data-animate><div class="timeline-dot" aria-hidden="true"></div><div class="timeline-year">2003</div><h3>O Marco Inicial — Surgimento do CoLT</h3><p>Realização do primeiro Congresso Brasileiro das Ligas do Trauma (CoLT), idealizado por estudantes e coordenado por importantes nomes da cirurgia do trauma no Brasil, dando início à mobilização nacional das ligas.</p></div>
      <div class="timeline-item" role="listitem" data-animate data-animate-delay="1"><div class="timeline-dot" aria-hidden="true"></div><div class="timeline-year">2003</div><h3>Fundação do CoBraLT</h3><p>Fundação oficial do Comitê Brasileiro das Ligas do Trauma, assumindo papel estratégico na articulação entre ligas, padronização de ações e fortalecimento institucional em todo o território nacional.</p></div>
      <div class="timeline-item" role="listitem" data-animate data-animate-delay="2"><div class="timeline-dot" aria-hidden="true"></div><div class="timeline-year">2013</div><h3>Expansão Nacional</h3><p>Criação de representações regionais e fortalecimento das ligas em todas as regiões do Brasil, consolidando uma rede ampla e integrada de ensino, pesquisa e extensão.</p></div>
      <div class="timeline-item" role="listitem" data-animate data-animate-delay="3"><div class="timeline-dot" aria-hidden="true"></div><div class="timeline-year">2016</div><h3>Protocolos Padronizados</h3><p>Implementação de protocolos clínicos padronizados nas ligas filiadas, aproximando o meio acadêmico da prática assistencial e reduzindo índices de morbimortalidade.</p></div>
      <div class="timeline-item" role="listitem" data-animate data-animate-delay="4"><div class="timeline-dot" aria-hidden="true"></div><div class="timeline-year">2020</div><h3>Transformação Digital</h3><p>Durante a pandemia de COVID-19, o CoBraLT adotou novas tecnologias, ampliando seu alcance, fortalecendo a comunicação nacional e modernizando a gestão da rede.</p></div>
      <div class="timeline-item" role="listitem" data-animate data-animate-delay="5"><div class="timeline-dot" aria-hidden="true"></div><div class="timeline-year">2026</div><h3>Reposicionamento Institucional</h3><p>A gestão do CoBraLT, após difíceis negociações com a Diretoria Executiva da SBAIT de 2024/2025, teve que tomar a difícil decisão de se desvincular da sociedade, que ainda não tem o devido reconhecimento pela Associação Médica Brasileira (AMB).</p></div>
      <div class="timeline-item" role="listitem" data-animate data-animate-delay="6"><div class="timeline-dot" aria-hidden="true"></div><div class="timeline-year">Hoje</div><h3>Referência Nacional em Trauma</h3><p>Principal organização de ligas acadêmicas de trauma do Brasil, reunindo centenas de instituições e milhares de estudantes e profissionais comprometidos com a melhoria da assistência ao paciente traumatizado.</p></div>
    </div>
    <div style="margin-top:2.5rem;" data-animate>
      <a href="pages/historia.php" class="btn btn-secondary">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
        Ler a história completa
      </a>
    </div>
  </div>
</section>

<!-- ═══ DIRETORIA ═════════════════════════════════════════ -->
<section id="diretoria" class="section directors-section">
  <div class="section-inner">
    <div class="section-header">
      <span class="section-label">Quem nos lidera</span>
      <h2 class="section-title">
        <a href="pages/diretoria.php" class="section-title-link">Diretoria CoBraLT</a>
        <a href="pages/diretoria.php" class="section-page-link">ver página →</a>
      </h2>
    </div>

    <?php if (empty($diretoria)): ?>
      <p style="color:var(--slate-400);">Nenhum membro cadastrado.</p>
    <?php else: ?>

      <?php /* Orientador — card especial */ ?>
      <?php if (!empty($diretoria['Orientador'])): $o = $diretoria['Orientador'][0]; ?>
      <div class="advisor-card">
        <div class="advisor-photo-wrap">
          <?php if ($o['foto']): ?>
          <img src="<?= h($o['foto']) ?>" alt="<?= h($o['nome']) ?>" class="director-photo" onerror="this.style.display='none'">
          <?php endif; ?>
        </div>
        <div class="advisor-info">
          <span class="director-role"><?= h($o['cargo']) ?></span>
          <h3 class="director-name"><?= h($o['nome']) ?></h3>
          <span class="director-specialty"><?= h($o['especialidade'] ?? '') ?></span>
          <?php if ($o['bio']): $bioItems = json_decode($o['bio'], true) ?? []; ?>
          <ul class="advisor-bio">
            <?php foreach ($bioItems as $item): ?>
            <li><?= h($item) ?></li>
            <?php endforeach; ?>
          </ul>
          <?php endif; ?>
        </div>
      </div>
      <?php endif; ?>

      <?php /* Demais grupos */ ?>
      <?php
      $ordemGrupos = [
          'Diretoria Deliberativa',
          'Diretoria de Marketing',
          'Diretoria Executiva',
          'Diretoria de Prevenção e Extensão',
          'Diretoria Interdisciplinar',
      ];
      foreach ($ordemGrupos as $grupo):
        if (empty($diretoria[$grupo])) continue;
      ?>
      <div class="directors-group">
        <h3 class="directors-group-title"><?= h($grupo) ?></h3>
        <div class="directors-grid">
          <?php foreach ($diretoria[$grupo] as $m):
            $semFoto = !$m['foto'] || $m['nome'] === 'Em definição';
          ?>
          <div class="director-card<?= $semFoto ? ' director-card--tbd' : '' ?>">
            <div class="director-photo-wrap<?= $semFoto ? ' director-photo-wrap--empty' : '' ?>">
              <?php if (!$semFoto): ?>
              <img src="<?= h($m['foto']) ?>" alt="<?= h($m['nome']) ?>" class="director-photo" onerror="this.style.display='none'">
              <?php endif; ?>
            </div>
            <div class="director-role"><?= h($m['cargo']) ?></div>
            <div class="director-name"><?= h($m['nome']) ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <?php endforeach; ?>

    <?php endif; ?>
  </div>
</section>

</main>

<!-- ═══ FOOTER ════════════════════════════════════════════ -->

<?php layout_footer('./', ['js/forms.js']); ?>
