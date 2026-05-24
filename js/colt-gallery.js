(function () {
  'use strict';

  const root = document.getElementById('coltGalleryRoot');
  if (!root) return;

  const editions = JSON.parse(root.dataset.editions || '[]');
  const bases = (root.dataset.bases || '../assets/img/Imagens Colts/|../assets/img/colts/|../assets/img/|../')
    .split('|')
    .map(item => item.trim())
    .filter(Boolean);
  const maxPhotos = Number(root.dataset.maxPhotos || 300);
  const emptyState = document.getElementById('coltGalleryEmpty');

  const lightbox = createLightbox();
  const lightboxItems = [];
  let currentLightboxIndex = 0;

  function photoExists(src) {
    return new Promise(resolve => {
      const img = new Image();
      img.onload = () => resolve(true);
      img.onerror = () => resolve(false);
      img.src = src;
    });
  }

  function buildUrl(base, folder, index) {
    const normalizedBase = base.endsWith('/') ? base : `${base}/`;
    return encodeURI(`${normalizedBase}${folder}/${index}.png`);
  }

  async function resolveBase(folder) {
    for (const base of bases) {
      const firstPhoto = buildUrl(base, folder, 1);
      if (await photoExists(firstPhoto)) return base;
    }
    return null;
  }

  async function collectPhotos(edition) {
    const folders = Array.isArray(edition.folders) && edition.folders.length
      ? edition.folders
      : [edition.folder];
    let base = null;
    let folder = null;

    for (const candidate of folders) {
      base = await resolveBase(candidate);
      if (base) {
        folder = candidate;
        break;
      }
    }

    if (!base || !folder) return [];

    const photos = [];
    for (let index = 1; index <= maxPhotos; index += 1) {
      const src = buildUrl(base, folder, index);
      if (!(await photoExists(src))) break;
      photos.push(src);
    }
    return photos;
  }

  function createLightbox() {
    const overlay = document.createElement('div');
    overlay.className = 'colt-lightbox';
    overlay.setAttribute('aria-hidden', 'true');
    overlay.innerHTML = `
      <div class="colt-lightbox-panel" role="dialog" aria-modal="true" aria-label="Visualizar foto">
        <button class="colt-lightbox-close" type="button" aria-label="Fechar foto">×</button>
        <button class="colt-lightbox-nav colt-lightbox-prev" type="button" aria-label="Foto anterior">‹</button>
        <img class="colt-lightbox-img" alt="">
        <button class="colt-lightbox-nav colt-lightbox-next" type="button" aria-label="Próxima foto">›</button>
        <div class="colt-lightbox-caption"></div>
      </div>
    `;
    document.body.appendChild(overlay);

    overlay.addEventListener('click', event => {
      if (event.target === overlay) closeLightbox();
    });
    overlay.querySelector('.colt-lightbox-close').addEventListener('click', closeLightbox);
    overlay.querySelector('.colt-lightbox-prev').addEventListener('click', () => showLightbox(currentLightboxIndex - 1));
    overlay.querySelector('.colt-lightbox-next').addEventListener('click', () => showLightbox(currentLightboxIndex + 1));

    document.addEventListener('keydown', event => {
      if (!overlay.classList.contains('open')) return;
      if (event.key === 'Escape') closeLightbox();
      if (event.key === 'ArrowLeft') showLightbox(currentLightboxIndex - 1);
      if (event.key === 'ArrowRight') showLightbox(currentLightboxIndex + 1);
    });

    return overlay;
  }

  function showLightbox(index) {
    if (!lightboxItems.length) return;
    currentLightboxIndex = (index + lightboxItems.length) % lightboxItems.length;
    const item = lightboxItems[currentLightboxIndex];
    const img = lightbox.querySelector('.colt-lightbox-img');
    const caption = lightbox.querySelector('.colt-lightbox-caption');

    img.src = item.src;
    img.alt = item.alt;
    caption.textContent = `${item.title} · foto ${item.number} de ${item.total}`;
    lightbox.classList.add('open');
    lightbox.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    lightbox.querySelector('.colt-lightbox-close').focus();
  }

  function closeLightbox() {
    lightbox.classList.remove('open');
    lightbox.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  function renderEdition(edition, photos) {
    if (!photos.length) return;

    const section = document.createElement('section');
    section.className = 'colt-gallery-edition';

    const meta = [edition.year, edition.place].filter(Boolean).join(' · ');
    section.innerHTML = `
      <div class="colt-gallery-edition-head">
        <div>
          <span class="section-label">${meta || 'Memória CoLT'}</span>
          <h2>${edition.edition}</h2>
        </div>
        <span class="colt-gallery-count">${photos.length} foto${photos.length === 1 ? '' : 's'}</span>
      </div>
      <div class="colt-gallery-grid"></div>
    `;

    const grid = section.querySelector('.colt-gallery-grid');
    photos.forEach((src, index) => {
      const title = edition.year ? `${edition.edition} ${edition.year}` : edition.edition;
      const item = {
        alt: `${title} - foto ${index + 1}`,
        number: index + 1,
        src,
        title,
        total: photos.length,
      };
      const lightboxIndex = lightboxItems.push(item) - 1;

      const button = document.createElement('button');
      button.type = 'button';
      button.className = 'colt-gallery-photo';
      button.setAttribute('aria-label', `Abrir ${item.alt}`);
      button.addEventListener('click', () => showLightbox(lightboxIndex));

      const img = document.createElement('img');
      img.src = src;
      img.alt = item.alt;
      img.loading = 'lazy';
      img.decoding = 'async';

      button.appendChild(img);
      grid.appendChild(button);
    });

    root.appendChild(section);
  }

  async function init() {
    let rendered = 0;
    for (const edition of editions) {
      const photos = await collectPhotos(edition);
      if (photos.length) {
        renderEdition(edition, photos);
        rendered += 1;
      }
    }

    root.classList.remove('is-loading');
    if (emptyState) emptyState.hidden = rendered > 0;
  }

  init().catch(() => {
    root.classList.remove('is-loading');
    if (emptyState) emptyState.hidden = false;
  });
})();
