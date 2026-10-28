/* Leitura de QR code de certificado no navegador (câmera ou imagem). Nada é enviado ao servidor
   até o código ser extraído; só o código (CBL-XXXX-XXXX-XXXX) é usado, nunca a URL lida. */
(function () {
  var root = document.getElementById('qr-tool');
  if (!root || !window.jsQR) return;
  root.hidden = false;

  var target = root.getAttribute('data-target');
  var msg = document.getElementById('qr-msg');
  var video = document.getElementById('qr-video');
  var CODE = /CBL-?[A-Z2-9]{4}-?[A-Z2-9]{4}-?[A-Z2-9]{4}/i;
  var stream = null;

  function stopCamera() {
    if (stream) stream.getTracks().forEach(function (t) { t.stop(); });
    stream = null;
    video.style.display = 'none';
  }

  function go(text) {
    var m = CODE.exec(text || '');
    if (!m) {
      msg.textContent = 'QR code lido, mas não é de um certificado do CoBraLT.';
      return false;
    }
    stopCamera();
    window.location.href = target + '?c=' + encodeURIComponent(m[0]);
    return true;
  }

  function decode(canvas) {
    var ctx = canvas.getContext('2d', { willReadFrequently: true });
    var d = ctx.getImageData(0, 0, canvas.width, canvas.height);
    var r = window.jsQR(d.data, d.width, d.height, { inversionAttempts: 'attemptBoth' });
    return r ? r.data : '';
  }

  document.getElementById('qr-file').addEventListener('change', function (e) {
    var file = e.target.files && e.target.files[0];
    if (!file) return;
    msg.textContent = 'Lendo imagem…';
    var img = new Image();
    img.onload = function () {
      var scale = Math.min(1, 1600 / Math.max(img.width, img.height));
      var c = document.createElement('canvas');
      c.width = Math.round(img.width * scale);
      c.height = Math.round(img.height * scale);
      c.getContext('2d').drawImage(img, 0, 0, c.width, c.height);
      URL.revokeObjectURL(img.src);
      var text = decode(c);
      if (!text) msg.textContent = 'Não encontrei um QR code nesta imagem. Tente uma foto mais nítida e bem enquadrada.';
      else go(text);
    };
    img.onerror = function () { msg.textContent = 'Não foi possível abrir esta imagem.'; };
    img.src = URL.createObjectURL(file);
  });

  document.getElementById('qr-camera').addEventListener('click', function () {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      msg.textContent = 'Seu navegador não permite usar a câmera aqui. Envie uma imagem do QR code.';
      return;
    }
    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } }).then(function (s) {
      stream = s;
      video.srcObject = s;
      video.style.display = 'block';
      video.play();
      msg.textContent = 'Aponte a câmera para o QR code…';
      var c = document.createElement('canvas');
      (function tick() {
        if (!stream) return;
        if (video.readyState === video.HAVE_ENOUGH_DATA) {
          c.width = video.videoWidth;
          c.height = video.videoHeight;
          c.getContext('2d').drawImage(video, 0, 0, c.width, c.height);
          var text = decode(c);
          if (text && go(text)) return;
        }
        requestAnimationFrame(tick);
      })();
    }).catch(function () {
      msg.textContent = 'Não foi possível acessar a câmera. Verifique a permissão ou envie uma imagem.';
    });
  });
})();
