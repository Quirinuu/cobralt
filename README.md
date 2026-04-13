# CoBraLT — Comitê Brasileiro das Ligas do Trauma

## Estrutura do Projeto

```
cobralT/
├── index.php               ← Página inicial (dinâmica, lê do banco)
├── config.php              ← Credenciais do banco e constantes globais
├── database.sql            ← Schema completo + dados iniciais (único arquivo)
├── .htaccess               ← Segurança, cache, HTTPS e reescritas
├── sitemap.xml             ← Sitemap para SEO
├── robots.txt
│
├── includes/
│   ├── db.php              ← Conexão PDO + função h() de escape
│   ├── layout.php          ← Header, footer e head compartilhados (layout_head/header/footer)
│   └── posts_helpers.php   ← fmtDate(), get_posts_by_tipo(), render_posts_grid()
│
├── pages/
│   ├── noticias.php        ← Lista de notícias (banco)
│   ├── eventos.php         ← Lista de eventos/posts de eventos (banco)
│   ├── projetos.php        ← Lista de projetos (banco)
│   ├── educacao.php        ← Lista de conteúdo educacional (banco)
│   ├── ligas.php           ← Ligas regionais (banco)
│   ├── diretoria.php       ← Diretoria (banco)
│   ├── historia.php        ← História do CoBraLT (estática, usa layout.php)
│   ├── post.php            ← Visualização de um post individual (?slug=xxx)
│   ├── login.html          ← Tela de login do admin
│   └── regiao-*.html       ← Páginas das ligas regionais (estáticas, a migrar futuramente)
│
├── admin/
│   ├── .htaccess           ← Bloqueia acesso direto a arquivos _auth
│   ├── _auth.php           ← Guard de autenticação (inclua em todo .php do admin)
│   ├── login.html          ← Login do painel admin
│   ├── dashboard.php       ← Painel principal
│   ├── posts.php           ← Listagem de posts
│   ├── post-editor.php     ← Criar/editar posts
│   ├── pages.php           ← Listagem de páginas
│   ├── page-editor.php     ← Criar/editar páginas
│   └── usuarios.php        ← Gerenciar usuários admin
│
├── api/
│   ├── login.php           ← Autenticação com rate limiting
│   ├── logout.php          ← Encerra sessão
│   ├── posts.php           ← CRUD de posts (admin)
│   ├── pages.php           ← CRUD de páginas (admin)
│   └── upload.php          ← Upload de imagens (admin)
│   ← FALTAM CRIAR:
│   ← inscricao.php         ← Salvar inscrições no COLT (referenciado em forms.js)
│   ← filiacao.php          ← Salvar solicitações de filiação (referenciado em forms.js)
│   ← contato.php           ← Formulário de contato (referenciado em forms.js)
│
├── css/
│   ├── vars.css            ← Variáveis CSS (cores, fontes, espaçamentos)
│   ├── base.css            ← Reset e estilos base
│   ├── style.css           ← Import central de todos os CSS
│   ├── header.css          ← Header e drawer mobile
│   ├── hero.css            ← Seção hero da index
│   ├── sections.css        ← Cards, grids, seções internas
│   ├── animations.css      ← Classes de animação (animated, data-animate-*)
│   └── responsive.css      ← Media queries
│
├── js/
│   ├── main.js             ← Scroll spy do nav + smooth scroll
│   ├── animations.js       ← IntersectionObserver (data-animate-*) + drawer mobile + header shadow
│   └── forms.js            ← Formulários async (ATENÇÃO: depende de APIs ainda não criadas)
│
└── assets/
    └── img/                ← Fotos da diretoria, logo, uploads
```

---

## Deploy na Hostinger — Passo a Passo

### 1. Banco de dados
1. Acesse o **phpMyAdmin** no painel da Hostinger
2. Selecione o banco de dados do site
3. Importe o arquivo `database.sql` (contém schema + dados iniciais)
4. Gere um hash seguro para o admin:
   ```bash
   php -r "echo password_hash('SuaSenhaForte!123', PASSWORD_BCRYPT, ['cost'=>12]);"
   ```
5. Atualize o hash no banco:
   ```sql
   UPDATE admin_users SET password_hash = 'HASH_GERADO' WHERE username = 'admin';
   ```

### 2. Configurar credenciais
Edite **`config.php`** com os dados do painel Hostinger → Banco de Dados:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'u123456789_cobralT');
define('DB_USER', 'u123456789_admin');
define('DB_PASS', 'SuaSenha');
define('SITE_URL', 'https://cobralT.org.br'); // ← sem barra no final
```

### 3. Upload dos arquivos
1. Acesse o **Gerenciador de Arquivos** da Hostinger → `/public_html/`
2. Faça upload de todos os arquivos do projeto
3. Certifique-se de que `.htaccess` foi incluído (arquivos ocultos)

### 4. Ativar HTTPS
1. Painel Hostinger → SSL → Ativar certificado gratuito
2. No `.htaccess`, descomente as linhas de redirecionamento HTTPS

### 5. Acesso ao admin
- URL: `seudominio.com.br/admin/login.html`
- Usuário: `admin`
- Senha: a definida no passo 1

---

## Segurança implementada
- Senhas em hash bcrypt (custo 12)
- Rate limiting: máx 5 tentativas de login por IP / 15 min
- CSRF token em formulários admin
- Sessão com regeneração de ID após login
- Cookie HttpOnly + SameSite=Strict
- Verificação de IP por sessão (anti session hijacking)
- Timeout de sessão: 4 horas de inatividade
- Headers HTTP de segurança via `.htaccess`

---

## Pendências conhecidas
- [ ] Criar `api/inscricao.php` — salvar inscrições no COLT no banco
- [ ] Criar `api/filiacao.php` — salvar solicitações de filiação
- [ ] Criar `api/contato.php` — formulário de contato geral
- [ ] Migrar páginas `regiao-*.html` para PHP com `layout.php`
- [ ] Preencher `SITE_URL` em `config.php` com o domínio real
- [ ] Atualizar `sitemap.xml` com slugs reais dos posts após publicar conteúdo
