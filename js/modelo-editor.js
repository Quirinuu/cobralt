/* Editor visual do modelo de certificado: o que você vê aqui é o que sai no PDF.
 * - clique na caixa para editar o texto no próprio certificado; arraste para mover; puxe a alça para a largura
 * - campos (nome, evento, horas…) entram como etiquetas coloridas, sem chaves nem códigos
 * - barra de ferramentas: fonte, tamanho, cor, negrito, itálico, alinhamento, inserir campo
 */
(function () {
  'use strict';
  var cfg = JSON.parse(document.getElementById('cfg').textContent);
  var $ = function (id) { return document.getElementById(id); };
  var stage = $('stage'), tbar = $('tbar'), fieldMenu = $('fieldMenu');
  var layout = cfg.layout, bodyText = cfg.bodyText || '';
  var PAGE_W = cfg.pageW;
  var LINE = 1.417;                                   // altura de linha do PDF (0,5 mm por pt)
  var FONTS = { Helvetica: 'Arial, Helvetica, sans-serif', Times: '"Times New Roman", Times, serif', Courier: '"Courier New", Courier, monospace' };
  var els = {}, sel = null, editing = null;

  var FIELDS = cfg.fields, SAMPLE = {}, LABEL = {};
  FIELDS.forEach(function (f) { SAMPLE[f.key] = f.sample; LABEL[f.key] = f.chip; });

  var TAGS = { name: 'Nome (vem da planilha)', body: 'Texto principal', sig1: 'Assinatura 1', sig2: 'Assinatura 2', qr: 'QR code', code: 'Código e link' };
  function isExtra(k) { return /^extra\d+$/.test(k); }
  function isText(k) { return k === 'body' || isExtra(k); }          // texto editável no próprio certificado
  function isStyled(k) { return isText(k) || k === 'name' || k === 'code'; }
  function tagOf(k) { return TAGS[k] || 'Texto livre'; }
  function getText(k) { return k === 'body' ? bodyText : String(layout[k].text || ''); }
  function setText(k, t) { if (k === 'body') bodyText = t; else layout[k].text = t; }
  function esc(t) { return String(t).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }
  function k1() { return stage.clientWidth / PAGE_W; }                // px por mm

  // ---------- fundo (PDF ou imagem) ----------
  var bgStatus = $('bgStatus');
  function say(t) { bgStatus.textContent = t; bgStatus.hidden = !t; }
  function renderPdfBackground() {
    if (!window.pdfjsLib) { say('Não foi possível carregar o leitor de PDF. O PDF final sai correto; recarregue a página para tentar de novo.'); return; }
    say('Carregando o desenho do seu PDF… (só demora na primeira vez)');
    pdfjsLib.GlobalWorkerOptions.workerSrc = '../js/vendor/pdfjs/pdf.worker.min.js';
    pdfjsLib.getDocument(cfg.bgPdf).promise.then(function (d) { return d.getPage(1); }).then(function (pg) {
      var v0 = pg.getViewport({ scale: 1 }), vp = pg.getViewport({ scale: 1600 / v0.width });
      var c = document.createElement('canvas'); c.width = vp.width; c.height = vp.height;
      return pg.render({ canvasContext: c.getContext('2d'), viewport: vp }).promise.then(function () {
        var url = c.toDataURL('image/jpeg', 0.88);
        stage.style.backgroundImage = 'url(' + url + ')';
        say('');
        var fd = new FormData();
        fd.append('action', 'save_preview'); fd.append('csrf_token', cfg.csrf); fd.append('background', cfg.bgName); fd.append('image', url);
        fetch('certificado-modelos.php', { method: 'POST', body: fd, credentials: 'same-origin' }).catch(function () {});
      });
    }).catch(function (e) {
      say('Não consegui desenhar o PDF de fundo aqui (' + (e && e.message ? e.message : e) + '). Você ainda pode posicionar as caixas; use "Prévia PDF" para conferir.');
    });
  }
  if (cfg.previewUrl) stage.style.backgroundImage = "url('" + cfg.previewUrl + "')";
  else if (cfg.bgPdf) renderPdfBackground();

  // ---------- texto <-> etiquetas ----------
  function chipHtml(key) { return '<span class="chip" contenteditable="false" data-f="' + key + '">' + esc(LABEL[key] || key) + '</span>'; }
  function toEditHtml(t) {
    return esc(t).replace(/\{([a-z_]+)\}/g, function (m, k) { return SAMPLE[k] !== undefined ? chipHtml(k) : m; }).replace(/\n/g, '<br>');
  }
  function toViewHtml(t) {          // igual ao PDF: campos preenchidos e a 1ª letra sempre maiúscula
    var plain = String(t).replace(/\{([a-z_]+)\}/g, function (m, k) { return SAMPLE[k] !== undefined ? SAMPLE[k] : m; });
    plain = plain.charAt(0).toUpperCase() + plain.slice(1);
    return esc(plain).replace(/\n/g, '<br>');
  }
  function serialize(root) {
    var out = '';
    (function walk(n) {
      n.childNodes.forEach(function (ch) {
        if (ch.nodeType === 3) out += ch.nodeValue.replace(/\u00a0/g, ' ').replace(/\u200b/g, '');
        else if (ch.nodeName === 'BR') out += '\n';
        else if (ch.classList && ch.classList.contains('chip')) out += '{' + ch.dataset.f + '}';
        else if (/^(DIV|P)$/.test(ch.nodeName)) { if (out && !/\n$/.test(out)) out += '\n'; walk(ch); }
        else walk(ch);
      });
    })(root);
    return out.replace(/\n+$/, '');
  }

  // ---------- desenho dos elementos ----------
  function signerOf(k) { return cfg.signers[cfg.chosen[k]] || null; }
  function content(k) {
    if (k === 'name') return esc(SAMPLE.nome).toUpperCase();
    if (k === 'code') return 'Código: CBL-XXXX-XXXX-XXXX<br>Verifique a autenticidade em<br>' + esc(SAMPLE.link);
    if (k === 'qr') return '<div class="qrph"></div>';
    if (k === 'sig1' || k === 'sig2') {
      var s = signerOf(k);
      return (s.image ? '<img src="certificado-modelos.php?asset=' + encodeURIComponent(s.image) + '" alt="">' : '<div style="height:28px"></div>') +
        '<div class="sigl"><b>' + esc(s.name) + '</b><br>' + esc(s.role || '') + '</div>';
    }
    return toViewHtml(getText(k)) || '<span class="ph">Clique para escrever…</span>';
  }
  function render(k) {
    var d = layout[k], e = els[k], c = e.querySelector('.c'), m = k1();
    e.style.left = d.x + '%'; e.style.top = d.y + '%'; e.style.width = d.w + '%';
    if (k === 'qr') e.style.height = (PAGE_W * d.w / 100 * m) + 'px';
    if (isStyled(k)) {
      c.style.fontFamily = FONTS[d.font] || FONTS.Helvetica;
      c.style.fontWeight = (d.style || '').indexOf('B') >= 0 ? '700' : '400';
      c.style.fontStyle = (d.style || '').indexOf('I') >= 0 ? 'italic' : 'normal';
      c.style.color = d.color; c.style.textAlign = { L: 'left', C: 'center', R: 'right' }[d.align] || 'left';
      c.style.lineHeight = LINE; c.style.padding = '0 ' + m + 'px';          // o PDF recua 1 mm nas laterais
      var px = d.size * 0.3528 * m; c.style.fontSize = px + 'px';
      c.style.whiteSpace = k === 'name' ? 'nowrap' : 'normal';
    }
    if (editing !== k) c.innerHTML = content(k);
    if (k === 'name') {                                   // o nome encolhe para caber, como no PDF
      var px2 = d.size * 0.3528 * m;
      while (c.scrollWidth > e.clientWidth + 1 && px2 > 10 * 0.3528 * m) { px2 -= 0.5; c.style.fontSize = px2 + 'px'; }
    }
  }
  function buildOne(k) {
    if ((k === 'sig1' || k === 'sig2') && !signerOf(k)) return;      // sem assinante escolhido não há o que posicionar
    var e = document.createElement('div'); e.className = 'el'; e.dataset.key = k;
    e.innerHTML = '<span class="tag">' + esc(tagOf(k)) + '</span><div class="c"></div><i class="hr" title="Arraste para mudar a largura"></i>';
    stage.appendChild(e); els[k] = e; render(k);
    e.addEventListener('pointerdown', function (ev) { onDown(ev, k); });
    e.querySelector('.hr').addEventListener('pointerdown', function (ev) { ev.stopPropagation(); select(k); resize(ev, k); });
  }
  function buildAll() { Object.keys(layout).forEach(function (k) { if (layout[k] && typeof layout[k] === 'object') buildOne(k); }); }

  // ---------- seleção, arrastar, redimensionar ----------
  function select(k) {
    if (sel && els[sel]) els[sel].classList.remove('sel');
    sel = k;
    if (k && els[k]) els[k].classList.add('sel');
    syncToolbar();
  }
  function onDown(ev, k) {
    if (editing === k) return;                            // editando: o clique posiciona o cursor
    if (editing) exitEdit();
    ev.preventDefault();
    select(k);
    var d = layout[k], sx = ev.clientX, sy = ev.clientY, ox = d.x, oy = d.y, r = stage.getBoundingClientRect(), moved = false;
    function move(m) {
      if (!moved && Math.abs(m.clientX - sx) + Math.abs(m.clientY - sy) < 4) return;
      moved = true;
      d.x = Math.max(0, Math.min(100 - d.w, ox + (m.clientX - sx) / r.width * 100));
      d.y = Math.max(0, Math.min(98, oy + (m.clientY - sy) / r.height * 100));
      d.x = Math.round(d.x * 100) / 100; d.y = Math.round(d.y * 100) / 100; render(k);
    }
    function up() {
      document.removeEventListener('pointermove', move); document.removeEventListener('pointerup', up);
      if (!moved && isText(k)) enterEdit(k, sx, sy);      // clique simples num texto = editar
    }
    document.addEventListener('pointermove', move); document.addEventListener('pointerup', up);
  }
  function resize(ev, k) {
    ev.preventDefault();
    var d = layout[k], sx = ev.clientX, ow = d.w, r = stage.getBoundingClientRect();
    function move(m) { d.w = Math.max(4, Math.min(100 - d.x, Math.round((ow + (m.clientX - sx) / r.width * 100) * 10) / 10)); render(k); }
    function up() { document.removeEventListener('pointermove', move); document.removeEventListener('pointerup', up); }
    document.addEventListener('pointermove', move); document.addEventListener('pointerup', up);
  }

  // ---------- edição do texto no próprio certificado ----------
  function enterEdit(k, cx, cy) {
    if (!isText(k)) return;
    editing = k; select(k);
    var e = els[k], c = e.querySelector('.c');
    e.classList.add('editing');
    c.innerHTML = toEditHtml(getText(k)); c.contentEditable = 'true'; c.focus();
    var range = null;
    if (cx !== undefined && document.caretRangeFromPoint) range = document.caretRangeFromPoint(cx, cy);
    var s = window.getSelection();
    if (range && c.contains(range.startContainer)) { s.removeAllRanges(); s.addRange(range); }
    else { var r2 = document.createRange(); r2.selectNodeContents(c); r2.collapse(false); s.removeAllRanges(); s.addRange(r2); }
    syncToolbar();
  }
  function exitEdit() {
    if (!editing) return;
    var k = editing, e = els[k], c = e.querySelector('.c');
    var t = serialize(c).slice(0, k === 'body' ? 600 : 300);
    setText(k, t);
    c.contentEditable = 'false'; e.classList.remove('editing'); editing = null;
    render(k); syncToolbar();
  }
  function insertField(key) {
    var k = sel;
    if (!k || !isText(k)) return;
    if (editing !== k) enterEdit(k);
    els[k].querySelector('.c').focus();
    document.execCommand('insertHTML', false, chipHtml(key) + '&#8203;');
  }
  stage.addEventListener('paste', function (ev) {                    // cola só o texto, sem formatação
    if (!editing) return;
    ev.preventDefault();
    document.execCommand('insertText', false, (ev.clipboardData || window.clipboardData).getData('text/plain'));
  });
  stage.addEventListener('keydown', function (ev) { if (editing && ev.key === 'Escape') { ev.preventDefault(); exitEdit(); } });

  // ---------- barra de ferramentas ----------
  var T = { font: $('t_font'), size: $('t_size'), color: $('t_color'), b: $('t_b'), i: $('t_i'), l: $('t_l'), c: $('t_c'), r: $('t_r'), field: $('t_field'), del: $('t_del'), hint: $('t_hint') };
  function syncToolbar() {
    var d = sel && layout[sel], styled = !!sel && isStyled(sel);
    ['font', 'size', 'color', 'b', 'i', 'l', 'c', 'r'].forEach(function (n) { T[n].disabled = !styled; });
    $('t_minus').disabled = $('t_plus').disabled = !styled;
    T.field.disabled = !(sel && isText(sel));
    T.del.disabled = !(sel && isExtra(sel));
    if (styled) {
      T.font.value = d.font || 'Helvetica'; T.size.value = Math.round(d.size * 10) / 10; T.color.value = d.color || '#000000';
      T.b.classList.toggle('on', (d.style || '').indexOf('B') >= 0); T.i.classList.toggle('on', (d.style || '').indexOf('I') >= 0);
      T.l.classList.toggle('on', d.align === 'L'); T.c.classList.toggle('on', d.align === 'C'); T.r.classList.toggle('on', d.align === 'R');
    }
    T.hint.textContent = !sel ? 'Clique numa caixa para editar. Arraste para mover e puxe a alça azul para mudar a largura.'
      : editing ? 'Escrevendo… use "＋ Inserir campo" para colocar nome, evento, horas, data. Esc ou clique fora para terminar.'
      : sel === 'name' ? 'O nome de cada pessoa vem da planilha. Aqui você ajusta fonte, tamanho, cor e posição.'
      : sel === 'code' ? 'Código e link de verificação: ajuste fonte, tamanho e posição.'
      : sel === 'qr' ? 'QR code de verificação: arraste para mover; a alça muda o tamanho.'
      : isText(sel) ? 'Clique de novo no texto para editar.' : 'Arraste para mover; a alça muda a largura.';
  }
  function setProp(p, v) { if (!sel || !isStyled(sel)) return; layout[sel][p] = v; render(sel); }
  function keep(el) { el.addEventListener('mousedown', function (e) { e.preventDefault(); }); }   // não tira o foco/seleção do texto
  [T.b, T.i, T.l, T.c, T.r, T.field, T.del, $('t_minus'), $('t_plus')].forEach(keep);
  T.font.addEventListener('change', function () { setProp('font', T.font.value); });
  T.color.addEventListener('input', function () { setProp('color', T.color.value); });
  T.size.addEventListener('input', function () { var v = parseFloat(T.size.value); if (v >= 6 && v <= 80) { setProp('size', v); } });
  $('t_minus').addEventListener('click', function () { if (sel) { setProp('size', Math.max(6, (layout[sel].size || 12) - 1)); syncToolbar(); } });
  $('t_plus').addEventListener('click', function () { if (sel) { setProp('size', Math.min(80, (layout[sel].size || 12) + 1)); syncToolbar(); } });
  function toggle(flag) {
    if (!sel || !isStyled(sel)) return;
    var s = layout[sel].style || '', has = s.indexOf(flag) >= 0;
    s = has ? s.replace(flag, '') : s + flag;
    layout[sel].style = (s.indexOf('B') >= 0 ? 'B' : '') + (s.indexOf('I') >= 0 ? 'I' : '');
    render(sel); syncToolbar();
  }
  T.b.addEventListener('click', function () { toggle('B'); });
  T.i.addEventListener('click', function () { toggle('I'); });
  T.l.addEventListener('click', function () { setProp('align', 'L'); syncToolbar(); });
  T.c.addEventListener('click', function () { setProp('align', 'C'); syncToolbar(); });
  T.r.addEventListener('click', function () { setProp('align', 'R'); syncToolbar(); });
  T.field.addEventListener('click', function () { fieldMenu.hidden = !fieldMenu.hidden; });
  fieldMenu.addEventListener('mousedown', function (e) { e.preventDefault(); });
  fieldMenu.querySelectorAll('button').forEach(function (b) {
    b.addEventListener('click', function () { fieldMenu.hidden = true; insertField(b.dataset.f); });
  });
  T.del.addEventListener('click', function () {
    if (!sel || !isExtra(sel)) return;
    var k = sel; editing = null; els[k].remove(); delete els[k]; delete layout[k]; select(null);
  });
  $('addExtra').addEventListener('click', function () {
    if (editing) exitEdit();
    var n = 1; while (layout['extra' + n]) n++;
    if (n > 12) return;
    var k = 'extra' + n;
    layout[k] = { text: 'Data: {data}', x: 10, y: 62, w: 30, size: 12, color: '#3C3C3C', align: 'L', font: 'Times', style: '' };
    buildOne(k); select(k); enterEdit(k);
  });

  // clique fora: termina a edição / tira a seleção
  document.addEventListener('pointerdown', function (ev) {
    if (!fieldMenu.hidden && !ev.target.closest('#fieldMenu') && !ev.target.closest('#t_field')) fieldMenu.hidden = true;
    if (ev.target.closest('.el') || ev.target.closest('#tbar')) return;
    if (editing) exitEdit();
    if (ev.target.closest('#stage')) select(null);
  });
  document.addEventListener('keydown', function (ev) {                // setas movem, Delete remove (fora de campos de texto)
    if (editing || !sel || /^(INPUT|SELECT|TEXTAREA)$/.test(document.activeElement.tagName)) return;
    var d = layout[sel], step = ev.shiftKey ? 1 : 0.2;
    if (ev.key === 'ArrowLeft') d.x = Math.max(0, d.x - step);
    else if (ev.key === 'ArrowRight') d.x = Math.min(100 - d.w, d.x + step);
    else if (ev.key === 'ArrowUp') d.y = Math.max(0, d.y - step);
    else if (ev.key === 'ArrowDown') d.y = Math.min(98, d.y + step);
    else if (ev.key === 'Delete' && isExtra(sel)) { T.del.click(); return; }
    else return;
    ev.preventDefault(); render(sel);
  });

  // ---------- assinantes e salvar ----------
  ['sig1', 'sig2'].forEach(function (k, i) {
    var s = document.querySelector('select[name="signer' + (i + 1) + '_id"]');
    if (!s) return;
    s.addEventListener('change', function () {
      cfg.chosen[k] = parseInt(s.value, 10) || 0;
      if (els[k]) { els[k].remove(); delete els[k]; if (sel === k) select(null); }
      buildOne(k);
    });
  });
  $('f').addEventListener('submit', function () {
    if (editing) exitEdit();
    $('layout_json').value = JSON.stringify(layout);
    $('body_text').value = bodyText;
  });
  window.addEventListener('resize', function () { Object.keys(els).forEach(render); });

  buildAll(); syncToolbar();
})();
