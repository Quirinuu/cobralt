<?php
session_start();
if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin — CoBraLT</title>
  <meta name="robots" content="noindex, nofollow">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --navy: #002B4E;
      --sky:  #0EA5E9;
      --sky-dark: #0284C7;
      --danger: #EF4444;
      --slate-200: #E2E8F0;
      --slate-400: #94A3B8;
      --slate-600: #475569;
      --slate-800: #1E293B;
      --font-display: 'Playfair Display', Georgia, serif;
      --font-body: 'DM Sans', sans-serif;
    }

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: var(--font-body);
      background: #F1F5F9;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 2rem;
    }

    .login-wrap {
      width: 100%;
      max-width: 420px;
    }

    .login-logo {
      text-align: center;
      margin-bottom: 2rem;
    }

    .logo-mark {
      width: 52px;
      height: 52px;
      background: linear-gradient(135deg, var(--navy) 0%, #003d62 100%);
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0 auto 0.75rem;
    }

    .logo-mark svg { width: 26px; height: 26px; fill: none; stroke: #fff; stroke-width: 2; stroke-linecap: round; }

    .login-logo h1 {
      font-family: var(--font-display);
      font-size: 1.5rem;
      font-weight: 800;
      color: var(--navy);
      letter-spacing: -0.02em;
    }

    .login-logo p {
      color: var(--slate-400);
      font-size: 0.85rem;
      margin-top: 4px;
    }

    .login-card {
      background: #fff;
      border-radius: 16px;
      padding: 2.5rem;
      box-shadow: 0 4px 24px rgba(0,43,78,.10), 0 1px 4px rgba(0,0,0,.06);
    }

    .login-card h2 {
      font-size: 1rem;
      font-weight: 700;
      color: var(--navy);
      margin-bottom: 1.5rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid var(--slate-200);
    }

    .form-group {
      margin-bottom: 1.25rem;
    }

    .form-group label {
      display: block;
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--slate-800);
      margin-bottom: 6px;
    }

    .form-group input {
      width: 100%;
      padding: 0.75rem 1rem;
      border: 1.5px solid var(--slate-200);
      border-radius: 8px;
      font-family: var(--font-body);
      font-size: 0.95rem;
      color: var(--slate-800);
      outline: none;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-group input:focus {
      border-color: var(--sky);
      box-shadow: 0 0 0 3px rgba(14,165,233,0.12);
    }

    .btn-submit {
      width: 100%;
      padding: 0.875rem;
      background: var(--navy);
      color: #fff;
      border: none;
      border-radius: 8px;
      font-family: var(--font-body);
      font-size: 0.95rem;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.2s, transform 0.15s;
      margin-top: 0.5rem;
    }

    .btn-submit:hover { background: var(--sky-dark); transform: translateY(-1px); }
    .btn-submit:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

    .alert {
      padding: 0.875rem 1rem;
      border-radius: 8px;
      font-size: 0.875rem;
      font-weight: 500;
      margin-top: 1rem;
      display: none;
    }

    .alert.show { display: block; }
    .alert-error { background: #FEE2E2; color: #991B1B; border: 1px solid #FECACA; }
    .alert-success { background: #D1FAE5; color: #065F46; border: 1px solid #A7F3D0; }

    .login-footer {
      text-align: center;
      margin-top: 1.5rem;
    }

    .login-footer a {
      color: var(--slate-400);
      text-decoration: none;
      font-size: 0.85rem;
      transition: color 0.15s;
    }

    .login-footer a:hover { color: var(--sky-dark); }

    /* Indicador de força de campo */
    .attempts-warning {
      background: #FEF3C7;
      border: 1px solid #FDE68A;
      color: #92400E;
      padding: 0.75rem 1rem;
      border-radius: 8px;
      font-size: 0.8rem;
      margin-bottom: 1rem;
      display: none;
    }
  </style>
</head>
<body>

<div class="login-wrap">
  <div class="login-logo">
    <div class="logo-mark">
      <svg viewBox="0 0 24 24"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
    </div>
    <h1>CoBraLT</h1>
    <p>Painel de administração</p>
  </div>

  <div class="login-card">
    <h2>Acesso restrito</h2>

    <div class="attempts-warning" id="attemptsWarning">
      ⚠️ Atenção: múltiplas tentativas detectadas.
    </div>

    <form id="loginForm" novalidate>
      <div class="form-group">
        <label for="username">Usuário</label>
        <input
          type="text"
          id="username"
          name="username"
          placeholder="Seu usuário"
          autocomplete="username"
          required
          autofocus
        >
      </div>
      <div class="form-group">
        <label for="password">Senha</label>
        <input
          type="password"
          id="password"
          name="password"
          placeholder="••••••••"
          autocomplete="current-password"
          required
        >
      </div>

      <button type="submit" class="btn-submit" id="submitBtn">Entrar</button>

      <div class="alert alert-error" id="alertMsg" role="alert"></div>
    </form>
  </div>

  <div class="login-footer">
    <a href="../index.php">← Voltar ao site</a>
  </div>
</div>

<script>
  'use strict';

  const form = document.getElementById('loginForm');
  const submitBtn = document.getElementById('submitBtn');
  const alertMsg = document.getElementById('alertMsg');
  const attemptsWarning = document.getElementById('attemptsWarning');

  let attempts = parseInt(sessionStorage.getItem('login_attempts') || '0');

  if (attempts >= 3) {
    attemptsWarning.style.display = 'block';
  }

  function showAlert(msg) {
    alertMsg.textContent = msg;
    alertMsg.className = 'alert alert-error show';
  }

  function hideAlert() {
    alertMsg.className = 'alert alert-error';
  }

  form.addEventListener('submit', async e => {
    e.preventDefault();
    hideAlert();

    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value;

    if (!username || !password) {
      showAlert('Preencha usuário e senha.');
      return;
    }

    submitBtn.disabled = true;
    submitBtn.textContent = 'Verificando...';

    try {
      const data = new FormData();
      data.append('username', username);
      data.append('password', password);

      const res = await fetch('../api/login.php', { method: 'POST', body: data });
      const json = await res.json();

      if (json.success) {
        sessionStorage.removeItem('login_attempts');
        submitBtn.textContent = 'Redirecionando...';
        window.location.href = 'dashboard.php';
      } else {
        attempts++;
        sessionStorage.setItem('login_attempts', attempts);

        if (attempts >= 3) {
          attemptsWarning.style.display = 'block';
        }

        showAlert(json.message || 'Usuário ou senha incorretos.');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Entrar';
        document.getElementById('password').value = '';
        document.getElementById('password').focus();
      }
    } catch {
      showAlert('Erro de conexão. Tente novamente.');
      submitBtn.disabled = false;
      submitBtn.textContent = 'Entrar';
    }
  });
</script>
</body>
</html>
