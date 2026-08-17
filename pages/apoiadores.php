<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/layout.php';
require_once dirname(__DIR__) . '/includes/page_builder.php';
require_once dirname(__DIR__) . '/includes/supporters.php';

if (pb_render_managed_page_if_exists('apoiadores', 'apoiadores')) { exit; }

$supporters = supporters_get_active();
$supporterCount = count($supporters);

layout_head('Apoiadores', 'Apoiadores do CoBraLT — Comitê Científico e orientadores que apoiam as Ligas do Trauma no Brasil.');
layout_header('apoiadores');
?>
<main id="main-content">

<div class="page-hero">
  <div class="page-hero-inner">
    <nav class="breadcrumb">
      <a href="../index">Início</a><span>›</span><span>Apoiadores</span>
    </nav>
    <div class="page-hero-label">Comitê Científico</div>
    <h1 class="page-hero-title">Nossos Apoiadores</h1>
    <p class="page-hero-sub">Profissionais e instituições que apoiam e orientam as ligas acadêmicas de trauma filiadas ao CoBraLT.</p>
    <div class="region-stats">
      <div class="region-stat">
        <div class="region-stat-value"><?= $supporterCount ?></div>
        <div class="region-stat-label">apoiadores</div>
      </div>
      <div class="region-stat">
        <div class="region-stat-value">15+</div>
        <div class="region-stat-label">instituições</div>
      </div>
      <div class="region-stat">
        <div class="region-stat-value">5</div>
        <div class="region-stat-label">regiões do Brasil</div>
      </div>
    </div>
  </div>
</div>

<section class="section" style="padding-top:3rem;" aria-labelledby="apoiadores-grid-title">
  <div class="section-inner" style="max-width:1100px;">
    <div class="section-header centered" data-animate>
      <div class="divider" style="margin:0 auto 1rem;" aria-hidden="true"></div>
      <span class="section-label">Rede de apoio CoBraLT</span>
      <h2 class="section-title" id="apoiadores-grid-title">Comitê de Apoiadores</h2>
    </div>

    <?php if (!$supporters): ?>
      <div class="empty-state">
        <h3>Novos apoiadores serão publicados em breve.</h3>
      </div>
    <?php else: ?>
      <div class="supporters-grid">
        <?php foreach ($supporters as $index => $supporter):
          $name = (string)$supporter['nome'];
          $image = supporter_image_src((string)$supporter['imagem'], '../');
          $delay = ($index % 4) + 1;
        ?>
          <div class="sup-card" data-animate data-animate-delay="<?= $delay ?>">
            <button class="sup-card-btn" type="button" onclick="openLightbox(this)" aria-label="Ver card de <?= h($name) ?> em tela cheia">
              <div class="sup-photo-wrap">
                <img src="<?= h($image) ?>?v=<?= ASSET_VERSION ?>" alt="<?= h($name) ?>" loading="lazy" onerror="this.hidden=true;this.nextElementSibling.hidden=false">
                <div class="sup-avatar" hidden><?= h(supporter_initials($name)) ?></div>
                <div class="sup-zoom-hint" aria-hidden="true">
                  <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35M11 8v6M8 11h6"/></svg>
                </div>
              </div>
            </button>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<div class="sup-lightbox" id="supLightbox" role="dialog" aria-modal="true" aria-label="Visualizar apoiador">
  <button class="sup-lightbox-close" id="lbClose" aria-label="Fechar">
    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
  </button>
  <button class="sup-lightbox-prev" id="lbPrev" aria-label="Anterior">
    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
  </button>
  <img class="sup-lightbox-img" id="lbImg" src="" alt="">
  <button class="sup-lightbox-next" id="lbNext" aria-label="Próximo">
    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
  </button>
</div>

<script>
(function () {
  'use strict';
  var cards = Array.from(document.querySelectorAll('.sup-card-btn'));
  var current = 0;
  var lb = document.getElementById('supLightbox');
  var lbImg = document.getElementById('lbImg');
  var lbClose = document.getElementById('lbClose');
  var lbPrev = document.getElementById('lbPrev');
  var lbNext = document.getElementById('lbNext');

  function imgOf(btn) {
    var img = btn.querySelector('img');
    return img ? { src: img.src, alt: img.alt } : { src: '', alt: '' };
  }
  function open(idx) {
    if (!cards.length) return;
    current = (idx + cards.length) % cards.length;
    var info = imgOf(cards[current]);
    lbImg.src = info.src;
    lbImg.alt = info.alt;
    lb.classList.add('open');
    document.body.style.overflow = 'hidden';
    lbClose.focus();
  }
  function close() {
    lb.classList.remove('open');
    document.body.style.overflow = '';
    if (cards[current]) cards[current].focus();
  }

  window.openLightbox = function (button) { open(cards.indexOf(button)); };
  lbClose.addEventListener('click', close);
  lbPrev.addEventListener('click', function () { open(current - 1); });
  lbNext.addEventListener('click', function () { open(current + 1); });
  lb.addEventListener('click', function (event) { if (event.target === lb) close(); });
  document.addEventListener('keydown', function (event) {
    if (!lb.classList.contains('open')) return;
    if (event.key === 'Escape') close();
    if (event.key === 'ArrowLeft') open(current - 1);
    if (event.key === 'ArrowRight') open(current + 1);
  });
})();
</script>

</main>
<?php layout_footer(); ?>
