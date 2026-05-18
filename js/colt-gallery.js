(function () {
  'use strict';

  const root = document.getElementById('coltGalleryRoot');
  if (!root) return;

  const editions = JSON.parse(root.dataset.editions || '[]');
  const bases = (root.dataset.bases || '../assets/img/colts/|../assets/img/|../')
    .split('|')
    .map(item => item.trim())
    .filter(Boolean);
  const maxPhotos = Number(root.dataset.maxPhotos || 300);
  const emptyState = document.getElementById('coltGalleryEmpty');

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
      const img = document.createElement('img');
      img.src = src;
      img.alt = `${edition.edition} — foto ${index + 1}`;
      img.loading = 'lazy';
      img.decoding = 'async';
      grid.appendChild(img);
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
