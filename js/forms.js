/* forms.js — Formulários async do CoBraLT
 *
 * ATENÇÃO — APIs pendentes de criação:
 *   api/inscricao.php  ← salvar inscrições no COLT
 *   api/filiacao.php   ← salvar solicitações de filiação
 *   api/contato.php    ← formulário de contato geral
 *
 * Enquanto esses arquivos não existirem, os respectivos
 * formulários vão exibir erro de conexão ao serem submetidos.
 * Crie os endpoints seguindo o padrão de api/posts.php
 * (responder JSON com { success: true|false, message: "..." }).
 */
'use strict';

function showAlert(container, type, message) {
  container.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
  container.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  setTimeout(() => { container.innerHTML = ''; }, 5000);
}

/* ─── FORMULÁRIO DE INSCRIÇÃO NO COLT ─────────────────── */
const coltForm = document.getElementById('form-inscricao');
const coltAlert = document.getElementById('alert-inscricao');

if (coltForm) {
  coltForm.addEventListener('submit', async e => {
    e.preventDefault();
    const btn = coltForm.querySelector('[type=submit]');
    btn.disabled = true;
    btn.textContent = 'Enviando...';

    const data = new FormData(coltForm);

    try {
      const res = await fetch('/api/inscricao.php', { method: 'POST', body: data });
      const json = await res.json();
      if (json.success) {
        showAlert(coltAlert, 'success', '✓ Inscrição realizada! Você receberá um e-mail de confirmação.');
        coltForm.reset();
      } else {
        showAlert(coltAlert, 'error', json.message || 'Erro ao enviar. Tente novamente.');
      }
    } catch {
      showAlert(coltAlert, 'error', 'Erro de conexão. Verifique sua internet.');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Confirmar Inscrição';
    }
  });
}

/* ─── FORMULÁRIO DE FILIAÇÃO ──────────────────────────── */
const filiacaoForm = document.getElementById('form-filiacao');
const filiacaoAlert = document.getElementById('alert-filiacao');

if (filiacaoForm) {
  filiacaoForm.addEventListener('submit', async e => {
    e.preventDefault();
    const btn = filiacaoForm.querySelector('[type=submit]');
    btn.disabled = true;
    btn.textContent = 'Enviando...';

    const data = new FormData(filiacaoForm);

    try {
      const res = await fetch('/api/filiacao.php', { method: 'POST', body: data });
      const json = await res.json();
      if (json.success) {
        showAlert(filiacaoAlert, 'success', '✓ Solicitação enviada! Nossa equipe analisará em até 5 dias úteis.');
        filiacaoForm.reset();
      } else {
        showAlert(filiacaoAlert, 'error', json.message || 'Erro ao enviar. Tente novamente.');
      }
    } catch {
      showAlert(filiacaoAlert, 'error', 'Erro de conexão. Verifique sua internet.');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Enviar Solicitação';
    }
  });
}

/* ─── FORMULÁRIO DE CONTATO ───────────────────────────── */
const contactForm = document.getElementById('form-contato');
const contactAlert = document.getElementById('alert-contato');

if (contactForm) {
  contactForm.addEventListener('submit', async e => {
    e.preventDefault();
    const btn = contactForm.querySelector('[type=submit]');
    btn.disabled = true;
    btn.textContent = 'Enviando...';

    const data = new FormData(contactForm);

    try {
      const res = await fetch('/api/contato.php', { method: 'POST', body: data });
      const json = await res.json();
      if (json.success) {
        showAlert(contactAlert, 'success', '✓ Mensagem enviada! Responderemos em breve.');
        contactForm.reset();
      } else {
        showAlert(contactAlert, 'error', json.message || 'Erro ao enviar. Tente novamente.');
      }
    } catch {
      showAlert(contactAlert, 'error', 'Erro de conexão. Verifique sua internet.');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Enviar Mensagem';
    }
  });
}
