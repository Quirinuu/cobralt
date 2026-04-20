<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/layout.php';

layout_head('XIV CoLT 2012', 'XIV CoLT 2012 — Rio de Janeiro. O maior congresso de ligas de trauma do Brasil.');
layout_header('', '../');
?>
<main id="main-content">

<style>
/* ── Gallery ───────────────────────────────────────────── */
.colt2012-gallery-wrapper {
  position: relative;
  border-radius: var(--radius-lg);
  overflow: hidden;
  background: rgba(0,0,0,0.4);
}
.colt2012-gallery {
  display: flex;
  overflow: hidden;
  position: relative;
  aspect-ratio: 16/9;
  max-height: 480px;
}
.gallery-slide {
  flex: 0 0 100%;
  min-width: 100%;
}
.gallery-slide img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}
.gallery-btn {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 42px; height: 42px;
  background: rgba(1,54,132,0.85);
  border: none;
  border-radius: 50%;
  color: #fff;
  cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: all var(--transition);
  z-index: 10;
  backdrop-filter: blur(4px);
}
.gallery-btn:hover { background: var(--navy); transform: translateY(-50%) scale(1.1); }
.gallery-prev { left: 12px; }
.gallery-next { right: 12px; }
.gallery-counter {
  position: absolute;
  bottom: 12px; right: 12px;
  background: rgba(0,0,0,0.55);
  color: #fff;
  font-size: 0.72rem;
  font-weight: 600;
  padding: 3px 10px;
  border-radius: 99px;
  backdrop-filter: blur(4px);
  z-index: 10;
}
.gallery-thumbs {
  display: flex;
  gap: 6px;
  padding: 10px 8px;
  overflow-x: auto;
  scrollbar-width: none;
  background: rgba(0,0,0,0.55);
}
.gallery-thumbs::-webkit-scrollbar { display: none; }
.gallery-thumb {
  flex: 0 0 60px;
  height: 42px;
  border: 2px solid transparent;
  border-radius: var(--radius-sm);
  overflow: hidden;
  cursor: pointer;
  padding: 0;
  background: none;
  transition: all var(--transition);
  opacity: 0.55;
}
.gallery-thumb img {
  width: 100%; height: 100%;
  object-fit: cover;
  display: block;
}
.gallery-thumb.active, .gallery-thumb:hover {
  border-color: var(--sky);
  opacity: 1;
}
/* ── benefit-card ──────────────────────────────────────── */
.benefit-card { background:var(--white); border:1px solid var(--slate-200); border-radius:var(--radius-lg); padding:1.4rem 1.5rem; display:flex; gap:1rem; align-items:flex-start; box-shadow:var(--shadow-sm); transition:all var(--transition); position:relative; overflow:hidden; }
.benefit-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,var(--sky),var(--navy)); transform:scaleX(0); transition:transform var(--transition); }
.benefit-card:hover { transform:translateY(-3px); box-shadow:var(--shadow-md); }
.benefit-card:hover::before { transform:scaleX(1); }
.benefit-icon { width:40px; height:40px; background:linear-gradient(135deg,rgba(14,165,233,0.12),rgba(1,54,132,0.1)); border-radius:var(--radius-md); display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; }
.benefit-content h4 { font-family:var(--font-display); color:var(--navy); font-size:0.9rem; font-weight:700; margin-bottom:0.35rem; }
.benefit-content p { color:var(--slate-600); font-size:0.8rem; line-height:1.65; margin:0; }
.benefit-content strong { color:var(--navy); }
/* ── Responsive ────────────────────────────────────────── */
@media (max-width: 700px) {
  .colt2012-grid { grid-template-columns: 1fr !important; }
  .colt2012-gallery { aspect-ratio: 4/3; max-height: 300px; }
  .gallery-thumb { flex: 0 0 48px; height: 34px; }
  .benefit-card { flex-direction:column; gap:0.75rem; }
}
</style>

<!-- HERO -->
<div class="page-hero" style="background:linear-gradient(135deg,var(--navy-dark) 0%,#002a55 50%,var(--navy-light) 100%);">
  <div class="page-hero-inner">
    <nav class="breadcrumb">
      <a href="../index.php">Início</a><span>›</span>
      <a href="eventos.php">Eventos e Congressos</a><span>›</span>
      <span>XIV CoLT 2012</span>
    </nav>
    <div class="page-hero-label" style="display:flex;align-items:center;gap:6px;">
      <span>🏆</span> Evento Histórico · Rio de Janeiro
    </div>
    <h1 class="page-hero-title">XIV CoLT 2012</h1>
    <p class="page-hero-sub">O maior encontro das ligas de trauma do Brasil — reunindo especialistas de 36 países em torno da excelência no atendimento ao trauma.</p>
    <div class="region-stats">
      <div class="region-stat">
        <div class="region-stat-value">3.500</div>
        <div class="region-stat-label">participantes</div>
      </div>
      <div class="region-stat">
        <div class="region-stat-value">72</div>
        <div class="region-stat-label">palestrantes estrangeiros</div>
      </div>
      <div class="region-stat">
        <div class="region-stat-value">36</div>
        <div class="region-stat-label">países representados</div>
      </div>
      <div class="region-stat">
        <div class="region-stat-value">30+</div>
        <div class="region-stat-label">sociedades internacionais</div>
      </div>
    </div>
  </div>
</div>

<!-- GALERIA CARROSSEL -->
<section class="section" style="padding-top:3rem;padding-bottom:2rem;background:var(--navy-dark);">
  <div class="section-inner" style="max-width:960px;">
    <div class="section-header centered" data-animate>
      <div class="divider" style="margin:0 auto 1rem;background:rgba(255,255,255,0.2);" aria-hidden="true"></div>
      <span class="section-label" style="color:var(--sky-light);">Galeria</span>
      <h2 class="section-title" style="color:#fff;">Memórias do XIV CoLT</h2>
    </div>

    <div class="colt2012-gallery-wrapper" data-animate>
      <div class="colt2012-gallery" id="gallery2012">
        <?php for($i=1;$i<=15;$i++): $n=str_pad($i,2,'0',STR_PAD_LEFT); ?>
        <div class="gallery-slide">
          <img src="../assets/img/colt2012/colt2012-<?= $n ?>.JPG" alt="XIV CoLT 2012 — foto <?= $i ?>" loading="lazy">
        </div>
        <?php endfor; ?>
        <!-- Fallback: se não usar PHP, use as tags abaixo -->
        <noscript>
          <div class="gallery-slide"><img src="../assets/img/colt2012/colt2012-01.JPG" alt="XIV CoLT 2012 — foto 1"></div>
        </noscript>
      </div>

      <!-- Controles do carrossel -->
      <button class="gallery-btn gallery-prev" id="galleryPrev" aria-label="Foto anterior">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
      </button>
      <button class="gallery-btn gallery-next" id="galleryNext" aria-label="Próxima foto">
        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
      </button>

      <div class="gallery-counter" id="galleryCounter">1 / 15</div>

      <!-- Thumbnails -->
      <div class="gallery-thumbs" id="galleryThumbs">
        <?php for($i=1;$i<=15;$i++): $n=str_pad($i,2,'0',STR_PAD_LEFT); ?>
        <button class="gallery-thumb<?= $i===1?' active':'' ?>" data-index="<?= $i-1 ?>" aria-label="Foto <?= $i ?>">
          <img src="../assets/img/colt2012/colt2012-<?= $n ?>.JPG" alt="" loading="lazy">
        </button>
        <?php endfor; ?>
      </div>
    </div>
  </div>
</section>

<!-- HTML fallback gallery (used when PHP is not running) -->
<script>
// Build gallery slides in pure JS if not already built by PHP
(function(){
  var gallery = document.getElementById('gallery2012');
  if (!gallery) return;
  var existing = gallery.querySelectorAll('.gallery-slide');
  if (existing.length >= 2) return; // already built by PHP
  gallery.innerHTML = '';
  for (var i = 1; i <= 15; i++) {
    var n = String(i).padStart(2, '0');
    var div = document.createElement('div');
    div.className = 'gallery-slide';
    var img = document.createElement('img');
    img.src = '../assets/img/colt2012/colt2012-' + n + '.JPG';
    img.alt = 'XIV CoLT 2012 — foto ' + i;
    img.loading = 'lazy';
    div.appendChild(img);
    gallery.appendChild(div);
  }
  // rebuild thumbs
  var thumbs = document.getElementById('galleryThumbs');
  if (thumbs) {
    thumbs.innerHTML = '';
    for (var j = 1; j <= 15; j++) {
      var nn = String(j).padStart(2, '0');
      var btn = document.createElement('button');
      btn.className = 'gallery-thumb' + (j===1?' active':'');
      btn.setAttribute('data-index', j-1);
      btn.setAttribute('aria-label', 'Foto ' + j);
      var timg = document.createElement('img');
      timg.src = '../assets/img/colt2012/colt2012-' + nn + '.JPG';
      timg.alt = '';
      timg.loading = 'lazy';
      btn.appendChild(timg);
      thumbs.appendChild(btn);
    }
  }
})();
</script>

<!-- SOBRE O EVENTO -->
<section class="section" style="padding-top:3rem;" aria-labelledby="sobre-title">
  <div class="section-inner" style="max-width:960px;">

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:2.5rem;align-items:start;" class="colt2012-grid">

      <div data-animate>
        <div class="divider" aria-hidden="true"></div>
        <span class="section-label">O Evento</span>
        <h2 class="section-title" id="sobre-title" style="margin-bottom:1.25rem;">Sobre o XIV CoLT</h2>
        <p style="color:var(--slate-600);line-height:1.8;font-size:0.95rem;margin-bottom:1rem;">
          Realizado em <strong>2012 no Rio de Janeiro</strong>, o XIV Congresso Brasileiro das Ligas do Trauma foi um marco histórico na trajetória do CoBraLT. Com dimensão verdadeiramente internacional, o evento consolidou o Brasil como um dos principais polos de discussão científica sobre trauma no mundo.
        </p>
        <p style="color:var(--slate-600);line-height:1.8;font-size:0.95rem;margin-bottom:1rem;">
          A edição reuniu <strong>3.500 participantes</strong>, entre estudantes, médicos, enfermeiros, fisioterapeutas e demais profissionais da área de trauma, provenientes de diversas regiões do Brasil e de outros países.
        </p>
        <p style="color:var(--slate-600);line-height:1.8;font-size:0.95rem;">
          Foram <strong>150 palestrantes brasileiros</strong> e <strong>72 palestrantes estrangeiros</strong> de <strong>36 países</strong> diferentes, além de representantes de mais de <strong>30 sociedades internacionais de trauma</strong>, tornando o XIV CoLT um dos maiores eventos científicos de trauma realizados no hemisfério sul.
        </p>
      </div>

      <div data-animate data-animate-delay="1">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">

          <div style="background:linear-gradient(135deg,var(--navy),var(--navy-light));border-radius:var(--radius-lg);padding:1.4rem;text-align:center;">
            <div style="font-family:var(--font-display);font-size:2.2rem;font-weight:800;color:#fff;line-height:1;">3.500</div>
            <div style="color:var(--sky-light);font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;margin-top:0.4rem;">Participantes</div>
          </div>

          <div style="background:linear-gradient(135deg,var(--sky-dark),var(--sky));border-radius:var(--radius-lg);padding:1.4rem;text-align:center;">
            <div style="font-family:var(--font-display);font-size:2.2rem;font-weight:800;color:#fff;line-height:1;">72</div>
            <div style="color:rgba(255,255,255,0.85);font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;margin-top:0.4rem;">Palestrantes Internacionais</div>
          </div>

          <div style="background:linear-gradient(135deg,#003f6b,var(--navy));border-radius:var(--radius-lg);padding:1.4rem;text-align:center;">
            <div style="font-family:var(--font-display);font-size:2.2rem;font-weight:800;color:#fff;line-height:1;">36</div>
            <div style="color:var(--sky-light);font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;margin-top:0.4rem;">Países</div>
          </div>

          <div style="background:linear-gradient(135deg,var(--navy-light),#003f6b);border-radius:var(--radius-lg);padding:1.4rem;text-align:center;">
            <div style="font-family:var(--font-display);font-size:2.2rem;font-weight:800;color:#fff;line-height:1;">30+</div>
            <div style="color:var(--sky-light);font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;margin-top:0.4rem;">Soc. Internacionais</div>
          </div>

          <div style="grid-column:1/-1;background:var(--off-white);border:1px solid var(--slate-200);border-radius:var(--radius-lg);padding:1.2rem;">
            <div style="font-size:0.7rem;font-weight:700;color:var(--navy);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:0.6rem;">Palestras & Conteúdo</div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:0.4rem 0;border-bottom:1px solid var(--slate-200);">
              <span style="font-size:0.82rem;color:var(--slate-600);">Palestrantes brasileiros</span>
              <span style="font-weight:700;color:var(--navy);font-size:0.9rem;">150</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:0.4rem 0;border-bottom:1px solid var(--slate-200);">
              <span style="font-size:0.82rem;color:var(--slate-600);">Palestrantes estrangeiros</span>
              <span style="font-weight:700;color:var(--navy);font-size:0.9rem;">72</span>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;padding:0.4rem 0;">
              <span style="font-size:0.82rem;color:var(--slate-600);">Total de palestrantes</span>
              <span style="font-weight:700;color:var(--sky-dark);font-size:0.95rem;">222</span>
            </div>
          </div>

        </div>
      </div>
    </div>

  </div>
</section>

<!-- DESTAQUES -->
<section class="section" style="background:var(--off-white);padding-top:2.5rem;padding-bottom:2.5rem;" aria-labelledby="destaques-title">
  <div class="section-inner" style="max-width:960px;">
    <div class="section-header centered" data-animate>
      <div class="divider" style="margin:0 auto 1rem;" aria-hidden="true"></div>
      <span class="section-label">Dimensão Internacional</span>
      <h2 class="section-title" id="destaques-title">Um Congresso Histórico</h2>
    </div>
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(270px,1fr));gap:1.1rem;margin-top:1.5rem;">

      <div class="benefit-card" data-animate>
        <div class="benefit-icon">🌍</div>
        <div class="benefit-content">
          <h4>Alcance Global</h4>
          <p>Com representantes de <strong>36 países</strong> e mais de <strong>30 sociedades internacionais de trauma</strong>, o XIV CoLT foi um dos maiores eventos científicos de trauma do hemisfério sul.</p>
        </div>
      </div>

      <div class="benefit-card" data-animate data-animate-delay="1">
        <div class="benefit-icon">🎙️</div>
        <div class="benefit-content">
          <h4>222 Especialistas</h4>
          <p>O congresso contou com <strong>150 palestrantes brasileiros</strong> e <strong>72 palestrantes estrangeiros</strong>, reunindo referências globais em cirurgia do trauma, emergência e cuidados críticos.</p>
        </div>
      </div>

      <div class="benefit-card" data-animate data-animate-delay="2">
        <div class="benefit-icon">🏙️</div>
        <div class="benefit-content">
          <h4>Rio de Janeiro</h4>
          <p>Sediado no <strong>Rio de Janeiro</strong>, a cidade maravilhosa recebeu milhares de profissionais e estudantes de saúde para cinco dias intensos de ciência, prática e networking em trauma.</p>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- NAVEGAÇÃO EVENTOS -->
<section class="section" style="padding-top:2rem;padding-bottom:2.5rem;">
  <div class="section-inner" style="max-width:960px;">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:1rem;" data-animate>
      <a href="eventos.php#realizados" class="btn btn-secondary">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
        Todos os eventos realizados
      </a>
      <a href="eventos.php" class="btn btn-secondary">
        Próximos eventos
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
      </a>
    </div>
  </div>
</section>

<script>
(function () {
  'use strict';
  var track   = document.getElementById('gallery2012');
  var counter = document.getElementById('galleryCounter');
  var thumbsEl= document.getElementById('galleryThumbs');
  var btnPrev = document.getElementById('galleryPrev');
  var btnNext = document.getElementById('galleryNext');

  if (!track) return;

  var current = 0;

  function slides() { return track.querySelectorAll('.gallery-slide'); }
  function thumbs() { return thumbsEl ? thumbsEl.querySelectorAll('.gallery-thumb') : []; }

  function goTo(idx) {
    var total = slides().length;
    if (total === 0) return;
    current = (idx + total) % total;
    track.style.transform = 'translateX(-' + (current * 100) + '%)';
    if (counter) counter.textContent = (current + 1) + ' / ' + total;
    thumbs().forEach(function(t, i) {
      t.classList.toggle('active', i === current);
    });
    // scroll active thumb into view
    var activeThumb = thumbsEl && thumbsEl.querySelectorAll('.gallery-thumb')[current];
    if (activeThumb) activeThumb.scrollIntoView({ block: 'nearest', inline: 'center', behavior: 'smooth' });
  }

  if (btnPrev) btnPrev.addEventListener('click', function() { goTo(current - 1); });
  if (btnNext) btnNext.addEventListener('click', function() { goTo(current + 1); });

  // Thumb clicks
  if (thumbsEl) {
    thumbsEl.addEventListener('click', function(e) {
      var btn = e.target.closest('.gallery-thumb');
      if (btn) goTo(parseInt(btn.dataset.index, 10));
    });
  }

  // Keyboard navigation
  document.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowLeft')  goTo(current - 1);
    if (e.key === 'ArrowRight') goTo(current + 1);
  });

  // Touch/swipe
  var startX = null;
  track.addEventListener('touchstart', function(e) { startX = e.touches[0].clientX; }, { passive: true });
  track.addEventListener('touchend', function(e) {
    if (startX === null) return;
    var diff = startX - e.changedTouches[0].clientX;
    if (Math.abs(diff) > 40) goTo(diff > 0 ? current + 1 : current - 1);
    startX = null;
  }, { passive: true });

  // Init
  goTo(0);
})();
</script>

</main>

<?php layout_footer('../'); ?>