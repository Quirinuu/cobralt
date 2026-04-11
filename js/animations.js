/* ═══════════════════════════════════════════════════════
   ANIMATIONS.JS — IntersectionObserver + Drawer mobile
   ═══════════════════════════════════════════════════════ */

(function () {

  /* ── Intersection Observer (animações de scroll) ──── */
  const animEls = document.querySelectorAll(
    '[data-animate],[data-animate-left],[data-animate-right],[data-animate-fade]'
  );
  if (animEls.length) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.classList.add('animated');
          io.unobserve(e.target);
        }
      });
    }, { threshold: 0.1 });
    animEls.forEach(el => io.observe(el));
  }

  /* ── Drawer lateral mobile ──────────────────────────── */
  const hamburger   = document.getElementById('hamburger');
  const drawer      = document.getElementById('mobileDrawer');
  const overlay     = document.getElementById('drawerOverlay');
  const closeBtn    = document.getElementById('drawerClose');
  const drawerLinks = drawer ? drawer.querySelectorAll('a') : [];

  function openDrawer() {
    drawer.classList.add('open');
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
    hamburger.setAttribute('aria-expanded', 'true');
  }

  function closeDrawer() {
    drawer.classList.remove('open');
    overlay.classList.remove('open');
    document.body.style.overflow = '';
    hamburger.setAttribute('aria-expanded', 'false');
  }

  if (hamburger) hamburger.addEventListener('click', openDrawer);
  if (closeBtn)  closeBtn.addEventListener('click', closeDrawer);
  if (overlay)   overlay.addEventListener('click', closeDrawer);
  drawerLinks.forEach(a => a.addEventListener('click', closeDrawer));

  /* Fechar com ESC */
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeDrawer();
  });

  /* ── Header scrolled shadow ─────────────────────────── */
  const header = document.querySelector('.site-header');
  if (header) {
    window.addEventListener('scroll', () => {
      header.classList.toggle('scrolled', window.scrollY > 10);
    }, { passive: true });
  }

})();
