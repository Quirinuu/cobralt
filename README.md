# CoBraLT — Comitê Brasileiro das Ligas do Trauma

## Estrutura do Projeto

```
cobralT/
├── index.html          ← Página principal
├── .htaccess           ← Segurança + cache + HTTPS
├── database.sqlasd        ← Execute no phpMyAdmin para criar o banco
├── README.md
│
├── css/
│   ├── vars.css        ← Variáveis (cores, fontes)
│   ├── style.css       ← Estilos principais
│   └── responsive.css  ← Media queries
│
├── js/
│   ├── main.js         ← Navegação, animações
│   └── forms.js        ← Formulários com fetch API
│
├── admin/
│   ├── .htaccess       ← Proteção da pasta admin
│   ├── _auth.php       ← Guard de autenticação (inclua em todo .php do admin)
│   ├── login.html      ← Tela de login
│   └── dashboard.php   ← Painel principal
│
├── api/
│   ├── login.php       ← Autenticação segura com rate limiting
│   ├── logout.php      ← Encerra sessão
│   ├── inscricao.php   ← (a criar) Salva inscrições no COLT
│   └── filiacao.php    ← (a criar) Salva solicitações de filiação
│
└── assets/
    ├── img/
    ├── logo/
    └── icons/
```

## Deploy na Hostinger — Passo a Passo

### 1. Banco de dados
1. Acesse o **phpMyAdmin** no painel da Hostinger
2. Crie um banco de dados novo (anote o nome, usuário e senha)
3. Importe o arquivo `database.sql`
4. Gere a senha do admin:
   ```
   php -r "echo password_hash('SuaSenhaForte!123', PASSWORD_BCRYPT, ['cost'=>12]);"
   ```
5. Atualize o hash no banco:
   ```sql
   UPDATE admin_users SET password_hash = 'HASH_GERADO' WHERE username = 'admin';
   ```

### 2. Configurar credenciais
Edite estas constantes em **`api/login.php`** e **`admin/_auth.php`**:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'SEU_BANCO');   // ex: u123456789_cobralT
define('DB_USER', 'SEU_USER');    // ex: u123456789_admin
define('DB_PASS', 'SUA_SENHA');
```

### 3. Upload dos arquivos
1. Acesse o **Gerenciador de Arquivos** da Hostinger
2. Navegue até `/public_html/`
3. **Apague** todos os arquivos do WordPress que estiverem lá
4. Faça upload de todos os arquivos deste projeto

### 4. Ativar HTTPS
1. No painel Hostinger → SSL → Ative o certificado gratuito
2. Depois, no `.htaccess` raiz, descomente as linhas de redirecionamento HTTPS

### 5. Primeiro acesso ao admin
- URL: `seusite.com.br/admin/login.html`
- Usuário: `admin`
- Senha: a que você escolheu no passo 1

## Segurança implementada
- Senhas em hash bcrypt (custo 12) — nunca texto puro
- Rate limiting: máx 5 tentativas de login por IP a cada 15 min
- CSRF token em todos os formulários admin
- Sessão PHP com regeneração de ID após login
- Cookie HttpOnly + SameSite=Strict
- Verificação de IP por sessão (detecta session hijacking)
- Timeout de sessão: 4 horas de inatividade
- Headers HTTP de segurança via .htaccess
- Acesso à pasta `admin/` bloqueado para arquivos `_`

## Próximos passos
- [ ] Criar `api/inscricao.php` e `api/filiacao.php`
- [ ] Criar `admin/post-editor.php` com editor WYSIWYG
- [ ] Criar `admin/posts.php` com listagem completa
- [ ] Criar `admin/pages.php` para gerenciar páginas
- [ ] Adicionar upload de imagens
- [ ] Integrar posts dinâmicos na página inicial via PHP ou JSON

TESTE TESTE TESTE