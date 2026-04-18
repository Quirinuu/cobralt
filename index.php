<?php
/**
 * index.php — CoBraLT
 * Página inicial. Dados vindos do banco — edite pelo painel admin/.
 */
declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/posts_helpers.php';

// ─── Busca os dados do banco ───────────────────────────────
try {
    $db = getPublicDB();

    // 3 notícias publicadas mais recentes
    $stmtNoticias = $db->query(
        "SELECT title, excerpt, category, published_at, slug
         FROM posts
         WHERE status = 'published'
         ORDER BY published_at DESC
         LIMIT 3"
    );
    $noticias = $stmtNoticias->fetchAll();

    // Eventos ativos, ordenados
    $stmtEventos = $db->query(
        "SELECT dia, mes, titulo, local, descricao, link, link_texto, link_externo
         FROM eventos
         WHERE ativo = 1
         ORDER BY ordem ASC
         LIMIT 3"
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
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="CoBraLT — Comitê Brasileiro das Ligas do Trauma. Conectando profissionais de saúde para salvar vidas.">
  <meta name="theme-color" content="#002B4E">
  <title>CoBraLT — Comitê Brasileiro das Ligas do Trauma</title>

  <!-- Favicon -->
  <link rel="icon" type="image/png" href="assets/img/logo.png">
  <link rel="apple-touch-icon" href="assets/img/logo.png">

  <!-- Open Graph -->
  <meta property="og:title" content="CoBraLT — Comitê Brasileiro das Ligas do Trauma">
  <meta property="og:description" content="Conectando as Ligas do Trauma para salvar vidas em todo o Brasil.">
  <meta property="og:image" content="https://cobralT.org.br/assets/img/logo.png">
  <meta property="og:type" content="website">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

  <!-- CSS -->
  <link rel="stylesheet" href="css/style.css?v=6.1">
</head>
<body id="inicio">

<!-- ═══ HEADER ══════════════════════════════════════════ -->
<header class="site-header" role="banner">
  <div class="header-inner">
    <a href="#inicio" class="logo has-image" aria-label="CoBraLT — Início">
      <img src="assets/img/logo.png?v=6.2" alt="CoBraLT — Comitê Brasileiro das Ligas do Trauma">
    </a>

    <nav class="nav-desktop" aria-label="Navegação principal">
      <a href="pages/noticias.php">Notícias</a>
      <a href="pages/eventos.php">Eventos</a>
      <a href="pages/projetos.php">Projetos</a>
      <a href="pages/educacao.php">Educação</a>
      <a href="pages/ligas.php">Ligas filiadas</a>
      <a href="apoiadores.html" >Apoiadores</a>
      <a href="pages/historia.php">História</a>
      <a href="pages/diretoria.php">Diretoria</a>
      <a href="https://www.instagram.com/cobralt_" target="_blank" rel="noopener noreferrer" class="nav-instagram" aria-label="Instagram do CoBraLT">
        <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
      </a>
      <a href="pages/login.html" class="nav-login"><svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>Admin</a>
    </nav>

    <button class="hamburger" id="hamburger" aria-label="Abrir menu" aria-expanded="false">
      <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true">
        <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
      </svg>
    </button>
  </div>
</header>

<div class="drawer-overlay" id="drawerOverlay" aria-hidden="true"></div>
<aside class="mobile-drawer" id="mobileDrawer" aria-label="Menu de navegação" role="dialog" aria-modal="true">
  <div class="drawer-header">
    <span class="drawer-header-logo">CoBraLT</span>
    <button class="drawer-close" id="drawerClose" aria-label="Fechar menu">
      <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>
  </div>
  <nav class="drawer-nav">
    <a href="pages/noticias.php">Notícias</a>
    <a href="pages/eventos.php">Eventos</a>
    <a href="pages/projetos.php">Projetos</a>
    <a href="pages/educacao.php">Educação</a>
    <a href="pages/ligas.php">Ligas filiadas</a>
    <a href="apoiadores.html">Apoiadores</a>
    <a href="pages/historia.php">História</a>
    <a href="pages/diretoria.php">Diretoria</a>
    <a href="https://www.instagram.com/cobralt_" target="_blank" rel="noopener noreferrer" style="display:flex;align-items:center;gap:8px;">
      <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
      Instagram
    </a>
    <a href="pages/login.html" class="nav-login">Admin</a>
  </nav>
</aside>

<main id="main-content">

<!-- ═══ HERO ═════════════════════════════════════════════ -->
<section class="hero" aria-label="Destaque">
  <div class="hero-bg" aria-hidden="true"></div>
  <div class="hero-grid" aria-hidden="true"></div>
  <div class="hero-inner">
    <div class="hero-content">
      <div class="hero-badge" aria-label="XXVIII Pré-CoLT — 17 de Abril, Campinas">
        <span class="hero-badge-dot" aria-hidden="true"></span>
        XXVIII Pré-CoLT — 17 de Abril · Campinas, SP
      </div>
      <h1 class="hero-title">Conectando as <span>Ligas do Trauma</span> para salvar vidas</h1>
      <p class="hero-subtitle">O Comitê Brasileiro das Ligas do Trauma reúne profissionais de saúde de todas as regiões do Brasil em torno da excelência no atendimento ao paciente grave.</p>
      <div class="hero-actions">
        <a href="#filiacao" class="btn btn-primary">
          <svg width="17" height="17" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
          Afiliar sua Liga
        </a>
        <a href="#ligas" class="btn btn-white-solid hero-highlight-btn">Mostrar Ligas</a>
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
    <div class="hero-visual hide-mobile" aria-hidden="true">
      <div class="hero-card-stack">
        <div class="hero-card-back"></div>
        <div class="hero-card">
          <div style="display:flex;align-items:center;gap:10px;margin-bottom:1.1rem;">
            <div style="width:34px;height:34px;background:rgba(14,165,233,0.2);border-radius:8px;display:flex;align-items:center;justify-content:center;">
              <svg width="17" height="17" fill="none" stroke="#38BDF8" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
            <div>
              <div style="color:rgba(255,255,255,0.5);font-size:0.66rem;text-transform:uppercase;letter-spacing:0.08em;">Próximo evento</div>
              <div style="color:#fff;font-weight:700;font-size:0.9rem;">XXVIII Pré-CoLT</div>
            </div>
          </div>
          <div class="colt-info-card"><div class="colt-label">Data</div><div class="colt-value">17 de Abril · Sexta-feira, 9h</div></div>
          <div class="colt-info-card"><div class="colt-label">Local</div><div class="colt-value">SMCC — Campinas, SP</div></div>
          <div class="colt-info-card" style="margin-bottom:0;"><div class="colt-label">Inscrições</div><div class="colt-value">Gratuitas · via QR Code</div></div>
          <a href="https://www.even3.com.br/ii-cotrem-657250/" target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:1.1rem;font-size:0.86rem;">Inscrever-se — Gratuito</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══ PRÉ-CoLT BANNER ══════════════════════════════════ -->
<section class="colt-banner" id="colt" aria-labelledby="colt-title">
  <div class="colt-grid">
    <div data-animate-left>
      <span class="section-label" style="color:var(--sky-light);">Evento Oficial CoBraLT</span>
      <h2 class="section-title" style="color:#fff;" id="colt-title">XXVIII Pré-CoLT<br>Congresso Brasileiro<br>de Ligas de Trauma</h2>
      <p style="color:rgba(255,255,255,0.6);font-size:0.98rem;line-height:1.75;margin:0.9rem 0 0;" data-animate-fade data-animate-delay="1">Edição Campinas — Um dia completo de ciência, prática e networking dedicado ao atendimento ao trauma, com especialistas nacionais e internacionais.</p>
      <div class="colt-details">
        <div class="colt-detail-item" data-animate data-animate-delay="1">
          <div class="colt-detail-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
          <div class="colt-detail-text"><strong>17 de Abril de 2026</strong><span>Sexta-feira, a partir das 9h00</span></div>
        </div>
        <div class="colt-detail-item" data-animate data-animate-delay="2">
          <div class="colt-detail-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
          <div class="colt-detail-text"><strong>SMCC — Campinas, SP</strong><span>Rua Delfino Cintra, 63, Centro</span></div>
        </div>
        <div class="colt-detail-item" data-animate data-animate-delay="3">
          <div class="colt-detail-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
          <div class="colt-detail-text"><strong>Realização: Unicamp · CoBraLT</strong><span>Disciplina de Cirurgia do Trauma</span></div>
        </div>
        <div class="colt-detail-item" data-animate data-animate-delay="4">
          <div class="colt-detail-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg></div>
          <div class="colt-detail-text"><strong>Inscrições gratuitas</strong><span>Acesse o link ou escaneie o QR Code</span></div>
        </div>
      </div>
      <div class="colt-actions" data-animate data-animate-delay="4">
        <a href="https://www.even3.com.br/ii-cotrem-657250/" target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:1.1rem;font-size:0.86rem;">Inscrever-se gratuitamente</a>
      </div>
    </div>
    <div data-animate-right>
      <div class="colt-box hide-mobile">
        <h3>Programa Científico</h3>
        <ul class="colt-list">
          <li><strong style="color:rgba(255,255,255,0.6);font-size:0.72rem;">9h–10h30</strong> Mass Casualty Management (MCM) — O preparo do HC Unicamp</li>
          <li><strong style="color:rgba(255,255,255,0.6);font-size:0.72rem;">11h–12h</strong> Vida no Trânsito: experiência em Campinas</li>
          <li><strong style="color:rgba(255,255,255,0.6);font-size:0.72rem;">13h30–15h</strong> P.A.R.T.Y. em Campinas + apresentações nacionais e internacionais</li>
          <li><strong style="color:rgba(255,255,255,0.6);font-size:0.72rem;">15h15–16h15</strong> Mesa-redonda: Estratégias de múltiplas vítimas</li>
          <li><strong style="color:rgba(255,255,255,0.6);font-size:0.72rem;">16h15–16h45</strong> Conferência: Doenças raras no pronto-socorro</li>
          <li><strong style="color:rgba(255,255,255,0.6);font-size:0.72rem;">16h45–18h</strong> Onda Amarela — Trabalhos no Congresso Europeu (Estocolmo)</li>
          <li><strong style="color:rgba(255,255,255,0.6);font-size:0.72rem;">18h00</strong> Encerramento</li>
        </ul>
      </div>
      <div class="info-grid">
        <div class="info-box" data-animate data-animate-delay="1"><div class="info-box-value">1</div><div class="info-box-label">Dia de evento</div></div>
        <div class="info-box" data-animate data-animate-delay="2"><div class="info-box-value">10+</div><div class="info-box-label">Palestrantes</div></div>
        <div class="info-box" data-animate data-animate-delay="3"><div class="info-box-value">Free</div><div class="info-box-label">Inscrição</div></div>
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
      <a href="pages/apoiadores.html" class="btn" style="font-size:0.85rem;padding:0.6rem 1.25rem;background:var(--white);border:1px solid var(--slate-200);color:var(--navy);display:inline-flex;align-items:center;gap:6px;box-shadow:var(--shadow-sm);">
        Ver todos →
      </a>
    </div>

    <!-- Carrossel de apoiadores -->
    <div class="sup-carousel-outer">
      <button class="sup-carousel-btn" id="supPrev" aria-label="Anterior">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
      </button>
      <div class="sup-viewport" id="supViewport">
        <div class="sup-track" id="supTrack">
          <?php
          $apoiadores = [
            ['nome'=>'Amauri Clemente da Rocha',           'foto'=>'amauri-clemente'],
            ['nome'=>'Antonio Toshimitsu Onimaru',          'foto'=>'antonio-toshimitsu'],
            ['nome'=>'Dr. Caio Duarte',                     'foto'=>'caio-duarte'],
            ['nome'=>'Cesar Vanderlei Carmona',             'foto'=>'cesar-vanderlei'],
            ['nome'=>'Cláudio Diunky Okawa',                'foto'=>'claudio-diunky'],
            ['nome'=>'Francisco Eduardo Silva',             'foto'=>'francisco-eduardo'],
            ['nome'=>'Frederico Michelino',                 'foto'=>'frederico-michelino'],
            ['nome'=>'Guilherme Biazotto',                  'foto'=>'guilherme-biazotto'],
            ['nome'=>'Gustavo de Mendonça Borges',          'foto'=>'gustavo-mendonca'],
            ['nome'=>'Gustavo P. Fraga',                    'foto'=>'gustavo-fraga'],
            ['nome'=>'Henrique José V. Silveira',           'foto'=>'henrique-jose'],
            ['nome'=>'Henrique Valério de Mesquita',        'foto'=>'henrique-valerio'],
            ['nome'=>'Jackson Vinícius de Lima Bertuol',    'foto'=>'jackson-vinicius'],
            ['nome'=>'José Alberto Fernandes da Silva Filho','foto'=>'jose-alberto'],
            ['nome'=>'Larissa Berbert',                     'foto'=>'larissa-berbert'],
            ['nome'=>'Nara Gelle de Oliveira',              'foto'=>'nara-gelle'],
            ['nome'=>'Phillipe Abreu',                      'foto'=>'phillipe-abreu'],
            ['nome'=>'Dr. Renato Diniz Lins',               'foto'=>'renato-diniz'],
            ['nome'=>'Rodrigo Barros de Carvalho',          'foto'=>'rodrigo-barros'],
            ['nome'=>'Rodrigo Caselli Belém',               'foto'=>'rodrigo-caselli'],
            ['nome'=>'Romeo L. Simões',                     'foto'=>'romeo-simoes'],
            ['nome'=>'Prof. Dr. Santiago Servin',           'foto'=>'santiago-servin'],
            ['nome'=>'Thiago R. Calderan',                  'foto'=>'thiago-calderan'],
            ['nome'=>'Tiago Leal Ghezzi',                   'foto'=>'tiago-leal'],
            ['nome'=>'Vitor F. Kruger',                     'foto'=>'vitor-kruger'],
            ['nome'=>'Waldemar Prandi Filho',               'foto'=>'waldemar-prandi'],
            ['nome'=>'Wellington José dos Santos',          'foto'=>'wellington-santos'],
            ['nome'=>'Willian G. Hashimoto H. de Sousa',   'foto'=>'willian-hashimoto'],
          ];
          foreach ($apoiadores as $ap): ?>
          <div class="sup-slide">
            <div class="sup-img-wrap">
              <img src="assets/img/apoiadores/<?= h($ap['foto']) ?>.png" alt="<?= h($ap['nome']) ?>" loading="lazy" onerror="this.style.opacity='0'">
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <button class="sup-carousel-btn" id="supNext" aria-label="Próximo">
        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
      </button>
    </div>
    <div class="sup-dots" id="supDots"></div>
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
@media (max-width: 640px) {
  .sup-slide { flex: 0 0 120px; }
  .sup-carousel-btn { width: 30px; height: 30px; }
}
@media (max-width: 400px) {
  .sup-slide { flex: 0 0 100px; }
}
</style>

<script>
(function () {
  const track   = document.getElementById('supTrack');
  const vp      = document.getElementById('supViewport');
  const dotsEl  = document.getElementById('supDots');
  const btnPrev = document.getElementById('supPrev');
  const btnNext = document.getElementById('supNext');
  if (!track || !vp) return;

  const slides = Array.from(track.querySelectorAll('.sup-slide'));
  const TOTAL  = slides.length;
  const GAP    = 14;   // px — deve bater com o gap do CSS (0.85rem ≈ 14px)
  const STEP   = 3;    // slides por clique/swipe
  const DELAY  = 2800; // ms entre avanços automáticos

  let current  = 0;
  let paused   = false; // pausa durante hover

  /* ── dimensões ── */
  function sw()      { return slides[0].offsetWidth + GAP; }
  function maxIdx()  { return Math.max(0, TOTAL - Math.round(vp.offsetWidth / sw())); }

  /* ── mover ── */
  function moveTo(idx, animated) {
    current = Math.max(0, Math.min(idx, maxIdx()));
    track.style.transition = animated ? 'transform 0.4s ease' : 'none';
    track.style.transform  = `translateX(${-(current * sw())}px)`;
    syncDots();
  }

  /* ── avançar (loop no fim) ── */
  function go(delta) {
    let next = current + delta;
    if (next > maxIdx()) next = 0;
    if (next < 0)        next = maxIdx();
    moveTo(next, true);
  }

  /* ── dots ── */
  const N = Math.min(7, TOTAL);
  const dots = Array.from({ length: N }, (_, i) => {
    const d = document.createElement('button');
    d.className = 'sup-dot';
    d.setAttribute('aria-label', 'Página ' + (i + 1));
    d.addEventListener('click', () => {
      moveTo(Math.round(i * maxIdx() / Math.max(1, N - 1)), true);
    });
    dotsEl.appendChild(d);
    return d;
  });

  function syncDots() {
    const mx = maxIdx();
    const active = mx > 0 ? Math.round((current / mx) * (N - 1)) : 0;
    dots.forEach((d, i) => d.classList.toggle('active', i === active));
  }

  /* ── setas ── */
  btnPrev.addEventListener('click', () => go(-STEP));
  btnNext.addEventListener('click', () => go(STEP));

  /* ── auto-play: ticker independente, não usa go() para não criar loops ── */
  setInterval(() => {
    if (!paused) {
      let next = current + 1;
      if (next > maxIdx()) next = 0;
      moveTo(next, true);
    }
  }, DELAY);

  /* ── pausa no hover (desktop) ── */
  vp.addEventListener('mouseenter', () => { paused = true; });
  vp.addEventListener('mouseleave', () => { paused = false; });

  /* ── swipe (mobile) ── */
  let tx0 = 0;
  vp.addEventListener('touchstart', e => { tx0 = e.touches[0].clientX; paused = true; }, { passive: true });
  vp.addEventListener('touchend',   e => {
    const d = tx0 - e.changedTouches[0].clientX;
    if (Math.abs(d) > 40) go(d > 0 ? STEP : -STEP);
    paused = false;
  }, { passive: true });

  /* ── init ── */
  moveTo(0, false);
})();
</script>

<!-- ═══ NOTÍCIAS ═════════════════════════════════════════ -->
<section class="section" id="noticias" aria-labelledby="noticias-title">
  <div class="section-inner">
    <div class="section-header" data-animate>
      <div class="divider" aria-hidden="true"></div>
      <span class="section-label">Informações</span>
      <h2 class="section-title" id="noticias-title">
        <a href="pages/noticias.php" class="section-title-link">Notícias Semanais</a>
        <a href="pages/noticias.php" class="section-page-link" aria-label="Ver todas as notícias">ver página →</a>
      </h2>
      <p class="section-subtitle">Fique atualizado sobre as últimas novidades do CoBraLT e do universo do trauma.</p>
    </div>

    <div class="news-grid">
      <?php if (empty($noticias)): ?>
        <p style="color:var(--slate-400);grid-column:1/-1;text-align:center;">Nenhuma notícia publicada ainda.</p>
      <?php else: foreach ($noticias as $i => $n):
        $cat   = h($n['category'] ?? 'Geral');
        $emoji = $catEmoji[$n['category']] ?? '📰';
        $dt    = $n['published_at'] ? fmtDate($n['published_at']) : '';
        $dtIso = $n['published_at'] ? substr($n['published_at'], 0, 10) : '';
      ?>
      <article class="news-card" data-animate data-animate-delay="<?= $i + 1 ?>">
        <div class="news-thumb">
          <span class="news-cat"><?= $cat ?></span>
          <?= $emoji ?>
        </div>
        <div class="news-body">
          <div class="news-meta">
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <time datetime="<?= h($dtIso) ?>"><?= h($dt) ?></time>
          </div>
          <h3><?= h($n['title']) ?></h3>
          <p><?= h($n['excerpt'] ?? '') ?></p>
          <a href="pages/post.php?slug=<?= h($n['slug']) ?>" class="news-link">
            Ler mais
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </a>
        </div>
      </article>
      <?php endforeach; endif; ?>
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
      <p class="section-subtitle">Mantenha-se atualizado com a agenda de eventos do CoBraLT e da área de trauma.</p>
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
        <a href="pages/colt2012.html" style="flex:0 0 220px;background:var(--white);border:1px solid var(--slate-200);border-radius:var(--radius-lg);padding:1.1rem;text-decoration:none;display:flex;flex-direction:column;gap:0.3rem;transition:all var(--transition);box-shadow:var(--shadow-sm);" onmouseover="this.style.transform='translateY(-3px)';this.style.boxShadow='var(--shadow-md)'" onmouseout="this.style.transform='';this.style.boxShadow='var(--shadow-sm)'">
          <span style="font-size:0.63rem;font-weight:700;text-transform:uppercase;letter-spacing:0.08em;background:linear-gradient(135deg,var(--navy),var(--sky-dark));color:#fff;padding:2px 8px;border-radius:99px;width:fit-content;">RJ · 2012</span>
          <div style="font-size:1.5rem;margin:0.2rem 0;">🏆</div>
          <div style="font-size:0.68rem;font-weight:700;color:var(--sky-dark);text-transform:uppercase;letter-spacing:0.1em;">XIV CoLT</div>
          <div style="font-family:var(--font-display);color:var(--navy);font-size:0.92rem;font-weight:800;">XIV CoLT 2012</div>
          <div style="color:var(--slate-400);font-size:0.72rem;">Rio de Janeiro, RJ</div>
          <div style="color:var(--slate-600);font-size:0.7rem;margin-top:0.25rem;">3.500 participantes · 72 palestrantes internacionais</div>
          <span style="font-size:0.75rem;font-weight:600;color:var(--sky-dark);margin-top:0.5rem;">Ver detalhes →</span>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ═══ CoLT + COTREM ══════════════════════════════════════ -->
<section class="section colt-banner" id="colt-cotrem" aria-labelledby="colt-cotrem-title" style="background:linear-gradient(135deg,var(--navy) 0%,#003f6b 100%);">
  <div class="section-inner" style="max-width:980px;">
    <div class="section-header centered" data-animate>
      <div class="divider" style="margin:0 auto 1rem;background:rgba(255,255,255,0.2);" aria-hidden="true"></div>
      <span class="section-label" style="color:var(--sky-light);">Evento Oficial CoBraLT · 2026</span>
      <h2 class="section-title" style="color:#fff;" id="colt-cotrem-title">
        XXVIII CoLT + II COTREM<br>
        <span style="font-size:0.65em;opacity:0.85;">Congresso Brasileiro das Ligas do Trauma<br>+ Congresso Internacional de Trauma e Emergências Médicas</span>
      </h2>
      <p class="section-subtitle" style="color:rgba(255,255,255,0.7);">21 a 23 de maio de 2026 · Formosa, Goiás · A maior edição conjunta da história do CoBraLT</p>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1rem;margin-bottom:2.5rem;" data-animate data-animate-delay="1">
      <div class="colt-info-card"><div class="colt-label">Data</div><div class="colt-value">21–23 de Maio · 08h–18h (GMT-3)</div></div>
      <div class="colt-info-card"><div class="colt-label">Local</div><div class="colt-value">A definir — Formosa, GO, Brasil</div></div>
      <div class="colt-info-card"><div class="colt-label">Formato</div><div class="colt-value">Presencial</div></div>
      <div class="colt-info-card"><div class="colt-label">Organização</div><div class="colt-value">UniRV – Campus Formosa + CoBraLT</div></div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;margin-bottom:2.5rem;" class="coltcotrem-grid" data-animate data-animate-delay="2">
      <div>
        <h3 style="font-family:var(--font-display);color:#fff;font-size:1.1rem;margin-bottom:1rem;">Sobre o Evento</h3>
        <p style="color:rgba(255,255,255,0.75);line-height:1.75;font-size:0.93rem;margin-bottom:0.9rem;">Após o grande sucesso do I COTREM, nasce em 2026 uma nova edição ainda mais robusta e inovadora. O evento acontece em conjunto com o <strong style="color:#fff;">XXVIII Congresso Brasileiro das Ligas do Trauma (CoLT)</strong>, unindo duas grandes iniciativas em um único encontro científico de alto nível.</p>
        <p style="color:rgba(255,255,255,0.75);line-height:1.75;font-size:0.93rem;">O II COTREM e o XXVIII CoLT representam um espaço de construção coletiva do conhecimento, incentivo à inovação e fortalecimento da assistência em trauma e emergências médicas no Brasil e no mundo.</p>
      </div>
      <div>
        <h3 style="font-family:var(--font-display);color:#fff;font-size:1.1rem;margin-bottom:1rem;">Patrocinadores</h3>
        <ul class="colt-list" style="margin-bottom:1.5rem;"><li>Tai Viagens</li><li>Auto Posto Somar</li><li>Samir Sahori Incorporadora</li><li>Clínica CER</li></ul>
        <div class="colt-actions" style="gap:0.75rem;flex-wrap:wrap;">
          <a href="https://www.even3.com.br/ii-cotrem-657250/" target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="font-size:0.88rem;padding:0.75rem 1.6rem;display:inline-flex;align-items:center;gap:0.5rem;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            Realizar inscrição
          </a>
          <a href="#atividades-cotrem" class="btn" style="font-size:0.88rem;padding:0.75rem 1.6rem;background:rgba(255,255,255,0.12);color:#fff;border:1px solid rgba(255,255,255,0.25);display:inline-flex;align-items:center;gap:0.5rem;">Ver atividades</a>
        </div>
      </div>
    </div>
    <div id="atividades-cotrem" class="hide-mobile" data-animate data-animate-delay="3">
      <h3 style="font-family:var(--font-display);color:#fff;font-size:1.1rem;margin-bottom:1.25rem;border-top:1px solid rgba(255,255,255,0.15);padding-top:1.5rem;">Atividades — Quinta, 21/05</h3>
      <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(270px,1fr));gap:0.75rem;">
        <div class="colt-box" style="padding:1rem 1.25rem;">
          <div style="font-size:0.72rem;font-weight:700;color:var(--sky-light);text-transform:uppercase;letter-spacing:0.07em;margin-bottom:0.6rem;">08:00 – 10:00</div>
          <ul class="colt-list" style="font-size:0.83rem;"><li>Workshop — Acesso Intraósseo</li><li>Workshop — Atendimento ao Paciente Neurocrítico</li><li>Workshop — Manejo da Via Aérea Difícil</li><li>Workshop — Manejo do paciente vítima de queimaduras</li><li>Workshop — Manejo do Trauma Torácico</li><li>Workshop — Mobilização com RMC</li><li>Workshop — Resgate de Submersão e Afogamento</li><li>Workshop — Stop The Bleed</li><li>Workshop — Técnicas em Sutura</li></ul>
        </div>
        <div class="colt-box" style="padding:1rem 1.25rem;">
          <div style="font-size:0.72rem;font-weight:700;color:var(--sky-light);text-transform:uppercase;letter-spacing:0.07em;margin-bottom:0.6rem;">10:30 – 12:30</div>
          <ul class="colt-list" style="font-size:0.83rem;"><li>Workshop — Acesso Central</li><li>Workshop — Acesso Intraósseo</li><li>Workshop — Imobilização de Fraturas</li><li>Workshop — Manejo da Via Aérea Difícil</li><li>Workshop — Manejo do Abdome Agudo Cirúrgico</li><li>Workshop — Mobilização com RMC</li><li>Workshop — Stop The Bleed</li><li>Workshop — Técnicas em Sutura</li></ul>
        </div>
        <div class="colt-box" style="padding:1rem 1.25rem;">
          <div style="font-size:0.72rem;font-weight:700;color:var(--sky-light);text-transform:uppercase;letter-spacing:0.07em;margin-bottom:0.6rem;">14:00 – 16:00</div>
          <ul class="colt-list" style="font-size:0.83rem;"><li>Workshop — Atendimento ao Paciente Neurocrítico</li><li>Workshop — Incidente com Múltiplas Vítimas</li><li>Workshop — Manejo do paciente vítima de queimaduras</li><li>Workshop — Manejo do Trauma Torácico</li><li>Workshop — Resgate de Submersão e Afogamento</li><li>Workshop — Técnicas em Sutura</li></ul>
        </div>
      </div>
      <div style="text-align:center;margin-top:1.5rem;">
        <a href="https://www.even3.com.br/ii-cotrem-657250/" target="_blank" rel="noopener noreferrer" class="btn btn-primary" style="font-size:0.9rem;padding:0.8rem 2rem;display:inline-flex;align-items:center;gap:0.5rem;">
          <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
          Quero participar das atividades
        </a>
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
            <div><div style="font-weight:600;color:var(--navy);font-size:0.84rem;">Descontos em Congressos</div><div style="color:var(--slate-600);font-size:0.78rem;line-height:1.5;">CoLT, eventos parceiros e acesso antecipado aos Pré-CoLTs</div></div>
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
        <a href="https://docs.google.com/forms/d/e/1FAIpQLSdxlJya6NcxHC1H_Cuao0mtN5_UehL-6RSzuhzj2JlEBtI9qA/viewform" target="_blank" rel="noopener noreferrer" class="btn btn-navy" style="font-size:0.95rem;padding:0.85rem 1.9rem;display:inline-flex;align-items:center;gap:0.5rem;">
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
      <a href="pages/historia.php" class="btn btn-navy" style="display:inline-flex;align-items:center;gap:.5rem;">
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
<footer class="site-footer" role="contentinfo">
  <div class="footer-inner">
    <div class="footer-top">
      <div class="footer-brand" data-animate-fade>
        <div class="logo" style="margin-bottom:1rem;">
          <div class="logo-mark"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg></div>
          <div class="logo-text"><span class="logo-name">CoBraLT</span><span class="logo-tagline">Ligas do Trauma</span></div>
        </div>
        <p>Conectando profissionais de saúde de todo o Brasil em torno da excelência no atendimento ao trauma.</p>
        <div class="social-links" style="margin-top:1rem;">
          <a href="https://www.instagram.com/cobralt_" target="_blank" rel="noopener noreferrer" class="social-link" aria-label="Instagram do CoBraLT">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" aria-hidden="true"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
          </a>
        </div>
      </div>
      <div class="footer-col" data-animate-fade data-animate-delay="1">
        <h4>Navegação</h4>
        <a href="#noticias">Notícias semanais</a>
        <a href="#eventos">Eventos e congressos</a>
        <a href="#filiacao">Filiar sua liga</a>
        <a href="#ligas">Ligas filiadas</a>
        <a href="pages/historia.php">História</a>
        <a href="pages/diretoria.php">Diretoria</a>
      </div>
      <div class="footer-col" data-animate-fade data-animate-delay="2">
        <h4>Eventos 2026</h4>
        <a href="#colt">Pré-CoLT — Edição Campinas</a>
        <a href="#colt-cotrem">XXVIII CoLT + II COTREM</a>
        <a href="#filiacao">Filiação de Ligas</a>
        <a href="#ligas">Ligas filiadas</a>
      </div>
      <div class="footer-col" data-animate-fade data-animate-delay="3">
        <h4>Legal</h4>
        <a href="pages/privacidade.php">Política de Privacidade</a>
        <a href="pages/termos.php">Termos de Uso</a>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© 2026 Comitê Brasileiro das Ligas do Trauma — CoBraLT. Todos os direitos reservados. <span style="opacity:.4;font-size:.78em;">Dev Gabriel Quirino</span></p>
      <div style="display:flex;gap:1.5rem;"><a href="pages/privacidade.php">Privacidade</a><a href="pages/termos.php">Termos de Uso</a></div>
    </div>
  </div>
</footer>

<!-- Scripts -->
<script src="js/main.js?v=6.2"></script>
<script src="js/forms.js?v=6.2"></script>
<script src="js/animations.js?v=6.2"></script>
</body>
</html>