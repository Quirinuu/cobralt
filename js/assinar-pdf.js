/* Prévia do PDF (pdf.js, no navegador) com selo arrastável. Preenche stamp_page/x/y/w para o servidor. */
(function () {
  if (!window.pdfjsLib) return;
  pdfjsLib.GlobalWorkerOptions.workerSrc = '../js/vendor/pdfjs/pdf.worker.min.js';

  var $ = function (id) { return document.getElementById(id); };
  var wrap = $('pvWrap'), file = $('pdfFile'), nav = $('pvNav');
  var f = { page: $('stamp_page'), x: $('stamp_x'), y: $('stamp_y'), w: $('stamp_w') };
  var doc = null, pageNo = 1, canvas = null, stamp = null;
  var st = { x: 70, y: 85, w: 22 };      // % da página (canto superior esquerdo do selo)
  var initial = false;
  var pageRatio = 297 / 210;             // largura/altura da página exibida
  var ASPECT = 0.3;                      // altura do selo = 30% da largura (igual ao PDF)

  function stampHeightPct() { return st.w * ASPECT * pageRatio; }   // altura em % da altura da página
  function clamp() {
    st.w = Math.max(14, Math.min(50, st.w));
    st.x = Math.max(0, Math.min(100 - st.w, st.x));
    st.y = Math.max(0, Math.min(100 - stampHeightPct(), st.y));
  }
  function sync() {
    clamp();
    f.x.value = st.x.toFixed(2); f.y.value = st.y.toFixed(2); f.w.value = st.w.toFixed(2);
    f.page.value = pageNo;
    if (stamp) { stamp.style.fontSize = Math.max(4, wrap.clientWidth * st.w / 100 * 0.062) + 'px'; stamp.style.left = st.x + '%'; stamp.style.top = st.y + '%'; stamp.style.width = st.w + '%'; stamp.style.height = stampHeightPct() + '%'; var im = stamp.querySelector('img'); if (im) im.style.maxHeight = (wrap.clientWidth * st.w / 100 * 0.09) + 'px'; }
    $('sizeLabel').textContent = Math.round(st.w) + '%'; $('sizeRange').value = st.w;
  }
  function esc(t) { return String(t).replace(/[&<>"']/g, function (c) { return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]; }); }

  function buildStamp() {
    if (stamp) stamp.remove();
    stamp = document.createElement('div'); stamp.className = 'stamp';
    var s = (window.SIGNERS || {})[$('signerSel').value];
    var sig = s ? (s.image ? '<img src="certificado-modelos.php?asset=' + encodeURIComponent(s.image) + '" alt="">' : '') + '<b>' + esc(s.name) + '</b>' : '';
    stamp.innerHTML = '<div class="qr"></div><div class="tx">' + sig + '<div><b>Código: CBL-XXXX-XXXX-XXXX</b></div><div>Verifique em …/verificar</div></div>';
    wrap.appendChild(stamp);
    stamp.addEventListener('pointerdown', function (ev) {
      ev.preventDefault();
      var r = wrap.getBoundingClientRect(), sx = ev.clientX, sy = ev.clientY, ox = st.x, oy = st.y;
      function move(m) { st.x = ox + (m.clientX - sx) / r.width * 100; st.y = oy + (m.clientY - sy) / r.height * 100; sync(); }
      function up() { document.removeEventListener('pointermove', move); document.removeEventListener('pointerup', up); }
      document.addEventListener('pointermove', move); document.addEventListener('pointerup', up);
    });
  }

  function render() {
    doc.getPage(pageNo).then(function (page) {
      var base = page.getViewport({ scale: 1 });
      pageRatio = base.width / base.height;
      var scale = Math.min(2, 880 / base.width);                // nítido, mas leve
      var vp = page.getViewport({ scale: scale });
      var c = document.createElement('canvas'); c.width = vp.width; c.height = vp.height;
      page.render({ canvasContext: c.getContext('2d'), viewport: vp }).promise.then(function () {
        wrap.innerHTML = ''; wrap.appendChild(c); canvas = c;
        if (initial) { st.w = 22; st.x = 100 - st.w - 3; st.y = 100 - stampHeightPct() - 3; initial = false; }
        buildStamp();
        $('pgLabel').textContent = 'Página ' + pageNo + ' de ' + doc.numPages;
        $('pgPrev').disabled = pageNo <= 1; $('pgNext').disabled = pageNo >= doc.numPages;
        sync();
      });
    });
  }

  file.addEventListener('change', function () {
    var fl = file.files && file.files[0];
    if (!fl) return;
    var rd = new FileReader();
    rd.onload = function () {
      pdfjsLib.getDocument({ data: new Uint8Array(rd.result) }).promise.then(function (d) {
        doc = d; pageNo = 1; nav.hidden = false;
        initial = true; render();   // posição inicial: canto inferior direito
      }).catch(function () {
        wrap.innerHTML = '<div class="pv-empty">Não foi possível abrir a prévia deste PDF. Você ainda pode enviar, mas a posição será a padrão (canto inferior direito).</div>';
        nav.hidden = true;
      });
    };
    rd.readAsArrayBuffer(fl);
  });

  $('pgPrev').onclick = function () { if (doc && pageNo > 1) { pageNo--; render(); } };
  $('pgNext').onclick = function () { if (doc && pageNo < doc.numPages) { pageNo++; render(); } };
  $('sizeRange').addEventListener('input', function (e) { st.w = parseFloat(e.target.value); sync(); });
  $('signerSel').addEventListener('change', function () { if (stamp) { buildStamp(); sync(); } });
  window.addEventListener('resize', sync);
})();
