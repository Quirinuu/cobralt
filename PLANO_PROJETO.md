# CoBraLT — Plano do projeto

Duas frentes: **(A) site 100% editável pelo admin** e **(B) autenticador de certificados**.
Este documento é só planejamento; nada aqui foi implementado ainda.

---

## 0. Diagnóstico (o que existe hoje)

| Conteúdo | Onde vive | Editável no admin? |
|---|---|---|
| Notícias | tabela `posts` | Sim (posts, post-editor) |
| Apoiadores | tabela `apoiadores` | Sim |
| Páginas livres / substituição de página por slug | tabela `pages` + page builder | Sim, mas a substituta começa em branco |
| Eventos (cards da home e /eventos) | tabela `eventos` | **Não** (sem tela) |
| Ligas por região (cards) | tabela `ligas_regioes` | **Não** |
| Diretoria | tabela `diretoria` (+ seed/restauração automática em `includes/directors.php`) | **Não** |
| Edições dos CoLTs (28) | array PHP em `includes/colt_editions.php` + fotos por pasta | **Não** |
| Menu e rodapé | `includes/layout.php` (links, Instagram, textos) | **Não** |
| Home (hero, seções, textos, CTA) | `index.php` | Só via substituição total da home |
| 7 páginas de região, historia, projetos, programas (7), boletins (3), prevenção (3), termos, privacidade | HTML escrito no PHP | Só via substituição total |
| Formulários recebidos | tabela `form_submissions` | Só leitura (admin/formularios.php) |
| Configurações (e-mail, Instagram, textos fixos) | espalhadas no código | **Não** |

---

## PARTE A — Tornar tudo editável

### A1. Estratégia

Princípio: **conteúdo vira dado, código vira molde.** Em vez de reescrever 40 páginas, separamos por tipo de conteúdo e damos a cada tipo a ferramenta certa:

1. **Configurações globais** (tabela `site_settings`, chave/valor): nome, e-mail de contato, Instagram, textos do rodapé, logo, SEO padrão. Lidas por `layout.php`.
2. **Menu e rodapé** (tabela `menu_items`: rótulo, URL, pai, ordem, local = topo/rodapé, ativo). `layout.php` passa a ler daqui, com o menu atual como seed.
3. **Entidades com CRUD próprio** (listar, criar, editar, ordenar, ativar/desativar, excluir): eventos, ligas, diretoria, edições dos CoLTs, boletins, programas/projetos. Cada uma vira tabela, com seed vindo do código atual.
4. **Seções da home como blocos**: a home passa a ser uma página do builder que já nasce com os blocos equivalentes ao layout atual (seed), em vez de PHP fixo. As listas dinâmicas (notícias, eventos, ligas, diretoria) viram **blocos "lista dinâmica"** novos no builder.
5. **Páginas textuais** (história, termos, privacidade, regiões, programas, prevenção): migrar o conteúdo atual para blocos do builder (seed), assim "Editar" abre o conteúdo existente, não uma página vazia. Mantém o fallback para o arquivo original.
6. **Biblioteca de mídia**: listar/enviar/excluir imagens e PDFs de `assets/img/uploads` e `assets/pdfs` num só lugar, com seletor reutilizável nos editores.
7. **Segurança de edição**: histórico de versões das páginas (restaurar anterior), "restaurar original", log de quem alterou o quê.

### A2. Ordem de execução (fases)

| Fase | Entrega | Por quê primeiro |
|---|---|---|
| 1 | `site_settings` + `menu_items` + tela "Configurações" | Destrava rodapé/menu em todas as páginas de uma vez |
| 2 | CRUD de eventos, ligas e diretoria | Tabelas já existem; só faltam telas. Maior ganho / menor esforço |
| 3 | Biblioteca de mídia + seletor de imagem | Todos os próximos editores usam |
| 4 | Edições dos CoLTs no banco (migrar array + galeria) | Hoje exige mexer em código para novo CoLT |
| 5 | Boletins e programas/projetos como entidades | Novos boletins/programas sem programador |
| 6 | Blocos "lista dinâmica" + home como página do builder | Home totalmente editável |
| 7 | Conversão em blocos das páginas textuais (seed) | Maior volume; fazer por lotes: termos/privacidade → história → regiões → programas |
| 8 | Histórico de versões, log de auditoria, perfis (editor/admin) | Segurança do conteúdo |

### A3. Checklist de verificação (a cada fase)
- Toda página pública tem hook `pb_render_managed_page_if_exists` (feito).
- Nenhum texto de usuário no código: `grep` por textos fixos em `pages/` e `index.php` ao fim de cada fase.
- Seeds só rodam quando a tabela está vazia e **nunca sobrescrevem edição do admin** (revisar `directors_seed_if_empty` e `apply_content_update_20260901`, que hoje reaplicam conteúdo no carregamento).
- Toda tela nova: `require_role`, `csrf_verify`, escape na saída, prepared statements.
- Teste em MySQL e em SQLite (o schema existe nos dois).

### A4. Riscos
- Conversão HTML→blocos é manual e demorada (páginas de região têm 400–800 linhas cada).
- O sanitizador de HTML do builder não permite `<img>`/`<div>`; páginas com layout rico precisam de blocos próprios.
- Deploy automático em `main`: trabalhar sempre em branch e testar localmente antes do merge.

---

## PARTE B — Autenticador de certificados

### B1. Escopo
Verificador **só para certificados emitidos por este site**. Qualquer pessoa digita o código (ou escaneia o QR do PDF) e vê se o certificado é válido. Admin emite, revoga e consulta.

### B2. Regras de negócio
- Cada certificado tem **código único e imprevisível** (ex.: `CBL-7K3Q-9M2X-4TDA`, 12 caracteres base32 aleatórios = ~60 bits; não sequencial, para impedir enumeração).
- Estados: `valido`, `revogado` (com motivo), opcional `expirado`.
- Verificação pública mostra apenas: nome, evento/curso, tipo (participante, palestrante, organizador...), carga horária, data de emissão, situação. **Sem CPF/e-mail** (LGPD).
- Emissão avulsa (formulário) e **em lote** (CSV: nome, e-mail, evento, horas...).
- Reemissão gera novo PDF com o mesmo código; revogar não apaga (fica auditável).
- Toda consulta é registrada (código, achou/não achou, IP, data) para detectar tentativas de adivinhação.

### B3. Banco de dados (nova migração em `database/migrations/`)

```
certificate_templates  id, name, background_image, layout_json (posições/fontes dos campos), active
certificates           id, code UNIQUE, holder_name, holder_email NULL, event_name, cert_type,
                       hours NULL, issued_at, status, revoked_at NULL, revoked_reason NULL,
                       template_id, pdf_path NULL, pdf_sha256 NULL, batch_id NULL, issued_by, created_at
certificate_batches    id, filename, total, created_by, created_at
certificate_checks     id, code_tried, found, ip, user_agent, created_at   (índice por ip + created_at)
```
Índices: `code` único; `holder_name` e `event_name` para a busca do admin. Compatível com MySQL e SQLite, seguindo o padrão de `db.php`.

### B4. Segurança do verificador
- Código aleatório via `random_bytes`; checagem de formato antes de ir ao banco (evita consulta inútil).
- Rate limit por IP (mesmo padrão do login, em banco): ex. 20 consultas/10 min.
- Mensagem idêntica para "não existe" e "formato inválido" (sem pista).
- Opcional: assinatura HMAC (segredo em `config.local.php`) embutida na URL do QR, validada no servidor, para detectar QR adulterado; e hash SHA-256 do PDF guardado, para provar que o arquivo é o emitido.
- Páginas e APIs com `X-Frame-Options`, sem cache para resultado, `noindex` na página de resultado.

---

## PARTE C — Frontend

### C1. Público
- **`/verificar`** (nova página, link no rodapé e no menu): campo do código com máscara `CBL-XXXX-XXXX-XXXX`, botão "Verificar", texto explicando onde achar o código. Também funciona via `/verificar?c=CODIGO` (destino do QR).
- **Resultado**: card verde "Certificado válido" com nome, evento, tipo, horas, data; card vermelho "Revogado" com data; card neutro "Código não encontrado". Acessível (aria-live, contraste) e responsivo.
- Segue o layout existente (`layout.php`, `css/`). Um arquivo novo `css/verificador.css` importado em `style.css`, e `js/verificar.js` fazendo `fetch` ao endpoint público (com fallback sem JS: o formulário também funciona por GET).
- Página editável pelo admin: o texto de apoio vem de `site_settings`/page builder.

### C2. Admin
Mesma estrutura visual das telas atuais (sidebar, cards, tabelas). Novo grupo no menu **"Certificados"**:
- **Lista** (`certificados.php`): busca por nome/código/evento, filtro por status e evento, paginação, ações: ver, baixar PDF, revogar/reativar, reemitir, copiar link de verificação.
- **Emitir** (`certificado-novo.php`): formulário avulso + aba "Importar CSV" com **prévia antes de confirmar** (linhas válidas/erros).
- **Modelos** (`certificado-modelos.php`): enviar imagem de fundo, posicionar nome/evento/horas/QR/código com pré-visualização ao vivo.
- **Log de verificações**: tabela simples com filtro "tentativas não encontradas".
- Perfis: editor não vê; admin e superadmin emitem; só superadmin exclui definitivamente.
- Telas de configuração geral e as novas telas da Parte A seguem o mesmo padrão (lista + editor) para parecerem uma coisa só.

---

## PARTE D — Backend

### D1. Estrutura de arquivos
```
includes/certificates.php          gerar código, validar formato, buscar, emitir, revogar, log
includes/certificate_pdf.php       montar PDF a partir do modelo + QR
api/verify.php                     público, GET ?code= → JSON, rate limit
api/certificates.php               admin: list/get/create/import/revoke/reissue (CSRF + role)
pages/verificar.php                página pública
admin/certificados.php, certificado-novo.php, certificado-modelos.php
database/migrations/2026XXXXXX_certificates.php
```

### D2. Geração do PDF (decisão necessária)
Hostinger compartilhada, sem Composer no projeto. Opções:
1. **Recomendada:** incluir no repositório **FPDF** (um arquivo) + gerador de QR em PHP puro (ex. `phpqrcode`/`qrcode.php` de uso livre), gravados em `vendor/` sem Composer. PDF gerado no servidor, salvo em `storage/certificados/` (pasta já bloqueada por `.htaccess`) e entregue por script autenticado.
2. HTML do certificado + "imprimir como PDF" no navegador. Zero dependência, mas o resultado varia por navegador e não dá para guardar o hash do arquivo.
3. Geração no navegador (JS) — descartada: o servidor não tem o arquivo oficial para conferir.

### D3. Fluxos
- **Emitir**: validar dados → gerar código único (tentar de novo em colisão) → inserir → gerar PDF → gravar caminho + SHA-256 → responder com link.
- **Importar CSV**: tamanho máximo, só `.csv`, UTF-8, limite de linhas (ex. 500), transação (tudo ou nada), prévia antes de gravar, proteção contra fórmula de planilha no que for exportado depois.
- **Verificar**: normaliza (maiúsculas, remove espaços/hífens) → valida formato → limite por IP → consulta → registra → devolve campos públicos.
- **Revogar**: exige motivo, registra quem e quando; página pública passa a mostrar "revogado".
- **Download do PDF**: só admin, via script que lê de `storage/` (arquivo nunca em pasta pública).

### D4. Pontos de segurança comuns
CSRF em toda escrita, `require_role`, prepared statements, escape na saída, validação de upload do modelo (mesma checagem de MIME do `api/upload.php`), nenhum dado pessoal no log público, backup do banco antes da migração.

### D5. Testes
Funções puras com testes simples em script PHP (`tests/certificates_test.php`): geração/validação de código, normalização, colisão, rate limit, revogação. Teste manual em SQLite local antes do deploy.

---

## Ordem geral sugerida
1. Fase A1 (configurações + menu) → A2 (eventos/ligas/diretoria) — ganho imediato.
2. **Certificados** (B/C/D) entra em seguida, em 4 etapas: migração + núcleo (`includes/certificates.php`, testes) → verificador público → admin de emissão → modelos de PDF e lote.
3. Restante da Parte A (mídia, CoLTs, boletins, home em blocos, conversão de páginas, histórico).

## Decisões que preciso de você
1. Biblioteca de PDF: posso vendorizar FPDF + gerador de QR (recomendado)?
2. O que aparece na verificação pública: nome completo, ou iniciais + evento?
3. Os certificados terão assinatura (imagem) de quem? Um ou vários signatários por modelo?
4. Envio por e-mail do certificado ao emitir é desejado (exige SMTP configurado)?
5. Quais tipos de certificado existem (participante, palestrante, organizador, monitor...)?
6. Já existem certificados emitidos antes (precisam ser importados com os códigos antigos)?
7. Começo pela Parte A, pelos certificados, ou em paralelo?
