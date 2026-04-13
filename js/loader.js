/**
 * loader.js — CoBraLT
 * Lê os arquivos JSON em /data e renderiza as seções do index dinamicamente.
 * Para atualizar Notícias, Eventos, Diretoria ou Ligas Afiliadas,
 * edite apenas o arquivo JSON correspondente em /data/.
 */

/* ── Caminho base para os JSONs ──
   Calculado a partir da URL do script (js/loader.js → ../data/)
   Funciona independente de onde o servidor está apontando. */
const _scriptEl = document.querySelector('script[src*="loader.js"]');
const _base = _scriptEl
  ? new URL('../data/', new URL(_scriptEl.src, location.href)).href
  : new URL('data/', location.href).href;

function dataURL(file) { return _base + file; }

/* ── Ícone de calendário (reutilizado) ── */
const ICON_CAL = `<svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="3" y1="10" x2="21" y2="10"/></svg>`;
const ICON_ARR = `<svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" viewBox="0 0 24 24" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>`;
const ICON_PIN = `<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>`;

/* ══════════════════════════════════════════════
   NOTÍCIAS
══════════════════════════════════════════════ */
async function loadNoticias() {
  const grid = document.getElementById('noticias-grid');
  if (!grid) return;

  try {
    const res  = await fetch(dataURL('noticias.json'));
    const list = await res.json();

    grid.innerHTML = list.map((n, i) => `
      <article class="news-card" data-animate data-animate-delay="${i + 1}">
        <div class="news-thumb">
          <span class="news-cat">${n.categoria}</span>
          ${n.emoji}
        </div>
        <div class="news-body">
          <div class="news-meta">
            ${ICON_CAL}
            <time datetime="${n.data_iso}">${n.data_exibicao}</time>
          </div>
          <h3>${n.titulo}</h3>
          <p>${n.resumo}</p>
          <a href="${n.link}" class="news-link">
            Ler mais
            ${ICON_ARR}
          </a>
        </div>
      </article>
    `).join('');
  } catch (e) {
    console.error('Erro ao carregar notícias:', e);
    grid.innerHTML = '<p style="color:var(--slate-400);grid-column:1/-1;">Não foi possível carregar as notícias.</p>';
  }
}

/* ══════════════════════════════════════════════
   EVENTOS
══════════════════════════════════════════════ */
async function loadEventos() {
  const grid = document.getElementById('eventos-grid');
  if (!grid) return;

  try {
    const res  = await fetch(dataURL('eventos.json'));
    const list = await res.json();

    grid.innerHTML = list.map((e, i) => `
      <div class="event-card" data-animate data-animate-delay="${i}">
        <div class="event-header">
          <div class="event-date-box">
            <div class="event-day" style="${e.dia_display.length > 2 ? 'font-size:1rem;line-height:1.1;' : ''}">${e.dia_display}</div>
            <div class="event-month">${e.mes}</div>
          </div>
          <h3 class="event-title">${e.titulo}</h3>
        </div>
        <div class="event-body">
          <div class="event-location">
            ${ICON_PIN}
            ${e.local}
          </div>
          <p class="event-desc">${e.descricao}</p>
          <a href="${e.link}"
             class="news-link"
             ${e.link_externo ? 'target="_blank" rel="noopener noreferrer"' : ''}>
            ${e.link_texto}
          </a>
        </div>
      </div>
    `).join('');
  } catch (err) {
    console.error('Erro ao carregar eventos:', err);
    grid.innerHTML = '<p style="color:var(--slate-400);grid-column:1/-1;">Não foi possível carregar os eventos.</p>';
  }
}

/* ══════════════════════════════════════════════
   LIGAS AFILIADAS
══════════════════════════════════════════════ */
async function loadLigas() {
  const grid = document.getElementById('ligas-grid');
  if (!grid) return;

  try {
    const res  = await fetch(dataURL('ligas.json'));
    const list = await res.json();

    grid.innerHTML = list.map((l, i) => `
      <div class="league-card" data-animate data-animate-delay="${i}">
        <div class="league-icon" aria-hidden="true">${l.emoji}</div>
        <h3>${l.nome}</h3>
        <p>${l.descricao}</p>
        <span class="league-count">${l.contagem}</span>
        <a href="${l.link}" class="news-link" style="margin-top:.75rem;display:inline-flex;">Ver ligas →</a>
      </div>
    `).join('');
  } catch (err) {
    console.error('Erro ao carregar ligas:', err);
    grid.innerHTML = '<p style="color:var(--slate-400);grid-column:1/-1;">Não foi possível carregar as ligas.</p>';
  }
}

/* ══════════════════════════════════════════════
   DIRETORIA
══════════════════════════════════════════════ */
function directorCard(m) {
  if (m.foto) {
    return `
      <div class="director-card">
        <div class="director-photo-wrap">
          <img src="${m.foto}" alt="${m.nome}" class="director-photo" onerror="this.style.display='none'">
        </div>
        <div class="director-role">${m.cargo}</div>
        <div class="director-name">${m.nome}</div>
      </div>`;
  }
  return `
    <div class="director-card director-card--tbd">
      <div class="director-photo-wrap director-photo-wrap--empty"></div>
      <div class="director-role">${m.cargo}</div>
      <div class="director-name">${m.nome}</div>
    </div>`;
}

async function loadDiretoria() {
  const container = document.getElementById('diretoria-container');
  if (!container) return;

  try {
    const res  = await fetch(dataURL('diretoria.json'));
    const data = await res.json();

    /* Orientador */
    const o = data.orientador;
    const orientadorHTML = `
      <div class="advisor-card">
        <div class="advisor-photo-wrap">
          <img src="${o.foto}" alt="${o.nome}" class="director-photo" onerror="this.style.display='none'">
        </div>
        <div class="advisor-info">
          <span class="director-role">${o.cargo}</span>
          <h3 class="director-name">${o.nome}</h3>
          <span class="director-specialty">${o.especialidade}</span>
          <ul class="advisor-bio">
            ${o.bio.map(b => `<li>${b}</li>`).join('')}
          </ul>
        </div>
      </div>`;

    /* Grupos */
    const gruposHTML = data.grupos.map(g => `
      <div class="directors-group">
        <h3 class="directors-group-title">${g.titulo}</h3>
        <div class="directors-grid">
          ${g.membros.map(directorCard).join('')}
        </div>
      </div>
    `).join('');

    container.innerHTML = orientadorHTML + gruposHTML;
  } catch (err) {
    console.error('Erro ao carregar diretoria:', err);
    container.innerHTML = '<p style="color:var(--slate-400);">Não foi possível carregar a diretoria.</p>';
  }
}

/* ══════════════════════════════════════════════
   INIT
══════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
  loadNoticias();
  loadEventos();
  loadLigas();
  loadDiretoria();
});
