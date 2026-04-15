<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/layout.php';

// Lê da tabela `eventos` (agenda), não de posts
try {
    $db     = getPublicDB();
    $eventos = $db->query(
        "SELECT dia, mes, titulo, local, descricao, link, link_texto, link_externo
         FROM eventos WHERE ativo = 1 ORDER BY ordem ASC"
    )->fetchAll();
} catch (PDOException $e) {
    $eventos = [];
}

layout_head('Eventos', 'Agenda completa de eventos e congressos do CoBraLT.');
layout_header('eventos');
?>

<main id="main-content">

<div class="page-hero">
  <div class="page-hero-inner">
    <nav class="breadcrumb">
      <a href="../index.php">Início</a><span>›</span><span>Eventos</span>
    </nav>
    <div class="page-hero-label">Agenda CoBraLT</div>
    <h1 class="page-hero-title">Eventos e Congressos</h1>
    <p class="page-hero-sub">Mantenha-se atualizado com a agenda de eventos do CoBraLT e da área de trauma.</p>
    <div class="region-stats">
      <div class="region-stat"><div class="region-stat-value"><?= count($eventos) ?></div><div class="region-stat-label">eventos</div></div>
      <div class="region-stat"><div class="region-stat-value">2026</div><div class="region-stat-label">ano em curso</div></div>
      <div class="region-stat"><div class="region-stat-value">180+</div><div class="region-stat-label">ligas na rede</div></div>
    </div>
  </div>
</div>

<section class="section" style="padding-top:3rem;">
  <div class="section-inner">
    <?php if (empty($eventos)): ?>
      <p style="color:var(--slate-400);text-align:center;padding:3rem 0;">Nenhum evento cadastrado.</p>
    <?php else: ?>
    <div class="events-grid">
      <?php foreach ($eventos as $i => $ev):
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
          <?php if ($ev['link']): ?>
          <a href="<?= h($ev['link']) ?>" class="news-link" <?= $extAttr ?>><?= h($ev['link_texto']) ?>
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </a>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- ═══ EVENTOS REALIZADOS ══════════════════════════════════ -->
<section class="section" id="realizados" style="background:linear-gradient(180deg,var(--off-white) 0%,#fff 100%);" aria-labelledby="realizados-title">
  <div class="section-inner" style="max-width:960px;">
    <div class="section-header centered" data-animate>
      <div class="divider" style="margin:0 auto 1rem;" aria-hidden="true"></div>
      <span class="section-label">Memória</span>
      <h2 class="section-title" id="realizados-title">Eventos Realizados</h2>
      <p class="section-subtitle">Conheça a história dos congressos e eventos que construíram a maior rede acadêmica de trauma do Brasil.</p>
    </div>

    <div class="events-carousel-wrapper" data-animate>
      <div class="events-carousel" id="eventsCarousel">

        <a href="colt2012.html" class="past-event-card">
          <div class="past-event-badge">RJ · 2012</div>
          <div class="past-event-icon">🏆</div>
          <div class="past-event-edition">XIV CoLT</div>
          <h3 class="past-event-name">XIV CoLT 2012</h3>
          <p class="past-event-city">
            <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            Rio de Janeiro, RJ
          </p>
          <div class="past-event-stats">
            <span>3.500 participantes</span>
            <span>72 palestrantes estrangeiros</span>
            <span>36 países representados</span>
          </div>
          <span class="past-event-link">Ver detalhes →</span>
        </a>

      </div>
      <div class="carousel-controls">
        <button class="carousel-btn" id="carouselPrev" aria-label="Anterior">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <div class="carousel-dots" id="carouselDots"></div>
        <button class="carousel-btn" id="carouselNext" aria-label="Próximo">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
      </div>
    </div>
  </div>
</section>

<style>
.events-carousel-wrapper { position:relative; }
.events-carousel { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:1.25rem; }
.events-carousel.is-overflow { display:flex; overflow-x:auto; scroll-snap-type:x mandatory; -webkit-overflow-scrolling:touch; scrollbar-width:none; padding-bottom:0.5rem; }
.events-carousel.is-overflow::-webkit-scrollbar { display:none; }
.past-event-card { background:var(--white); border:1px solid var(--slate-200); border-radius:var(--radius-lg); padding:1.5rem; scroll-snap-align:start; transition:all var(--transition); text-decoration:none; display:flex; flex-direction:column; gap:0.4rem; position:relative; overflow:hidden; color:inherit; box-shadow:var(--shadow-sm); }
.events-carousel.is-overflow .past-event-card { flex:0 0 300px; }
.past-event-card::before { content:''; position:absolute; top:0;left:0;right:0; height:3px; background:linear-gradient(90deg,var(--sky),var(--navy)); transform:scaleX(0); transition:transform var(--transition); }
.past-event-card:hover { transform:translateY(-4px); box-shadow:var(--shadow-lg); }
.past-event-card:hover::before { transform:scaleX(1); }
.past-event-badge { display:inline-flex; background:linear-gradient(135deg,var(--navy),var(--sky-dark)); color:#fff; font-size:0.65rem; font-weight:700; text-transform:uppercase; letter-spacing:0.08em; padding:2px 10px; border-radius:99px; margin-bottom:0.25rem; width:fit-content; }
.past-event-icon { font-size:2rem; margin-bottom:0.25rem; }
.past-event-edition { font-size:0.7rem; font-weight:700; color:var(--sky-dark); text-transform:uppercase; letter-spacing:0.1em; }
.past-event-name { font-family:var(--font-display); color:var(--navy); font-size:1.05rem; font-weight:800; margin:0.1rem 0 0.15rem; }
.past-event-city { color:var(--slate-400); font-size:0.75rem; display:flex; align-items:center; gap:4px; margin:0; }
.past-event-stats { display:flex; flex-direction:column; gap:3px; margin-top:0.5rem; }
.past-event-stats span { font-size:0.73rem; color:var(--slate-600); display:flex; align-items:center; gap:6px; }
.past-event-stats span::before { content:''; width:4px; height:4px; background:var(--sky); border-radius:50%; flex-shrink:0; }
.past-event-link { margin-top:auto; padding-top:0.85rem; font-size:0.78rem; font-weight:600; color:var(--sky-dark); }
.carousel-controls { display:none; align-items:center; justify-content:center; gap:1rem; margin-top:1.25rem; }
.events-carousel.is-overflow ~ .carousel-controls { display:flex; }
.carousel-btn { width:36px; height:36px; border-radius:50%; border:1px solid var(--slate-200); background:var(--white); color:var(--navy); display:flex; align-items:center; justify-content:center; cursor:pointer; transition:all var(--transition); box-shadow:var(--shadow-sm); }
.carousel-btn:hover { background:var(--navy); color:#fff; border-color:var(--navy); }
.carousel-dots { display:flex; gap:6px; align-items:center; }
.carousel-dot { width:7px; height:7px; border-radius:50%; background:var(--slate-200); transition:all var(--transition); cursor:pointer; border:none; padding:0; }
.carousel-dot.active { background:var(--navy); width:18px; border-radius:99px; }
@media (max-width:700px) { .events-carousel { grid-template-columns:1fr; } }
</style>

<script>
(function(){
  var carousel = document.getElementById('eventsCarousel');
  var prevBtn  = document.getElementById('carouselPrev');
  var nextBtn  = document.getElementById('carouselNext');
  var dotsWrap = document.getElementById('carouselDots');
  if (!carousel) return;
  var cards = carousel.querySelectorAll('.past-event-card');
  var total = cards.length;
  if (total >= 3) {
    carousel.classList.add('is-overflow');
    var current = 0, dots = [];
    for (var i=0;i<total;i++){
      var d=document.createElement('button');
      d.className='carousel-dot'+(i===0?' active':'');
      d.setAttribute('aria-label','Evento '+(i+1));
      (function(idx){ d.addEventListener('click',function(){ goTo(idx); }); })(i);
      dotsWrap.appendChild(d); dots.push(d);
    }
    function goTo(i){
      current=Math.max(0,Math.min(i,total-1));
      var cardW=cards[0]?cards[0].offsetWidth+20:0;
      carousel.scrollTo({left:current*cardW,behavior:'smooth'});
      dots.forEach(function(d,j){ d.classList.toggle('active',j===current); });
    }
    if(prevBtn) prevBtn.addEventListener('click',function(){ goTo(current-1); });
    if(nextBtn) nextBtn.addEventListener('click',function(){ goTo(current+1); });
    carousel.addEventListener('scroll',function(){
      var cardW=cards[0]?cards[0].offsetWidth+20:1;
      var idx=Math.round(carousel.scrollLeft/cardW);
      if(idx!==current){ current=idx; dots.forEach(function(d,j){ d.classList.toggle('active',j===current); }); }
    },{passive:true});
  }
})();
</script>

</main>

<?php layout_footer('../'); ?>
