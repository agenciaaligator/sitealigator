# Aligator — Site Institucional 2.0
## Tech Marketing Company

Sistema completo desenvolvido em **PHP + MySQL**, pronto para deploy em hospedagem compartilhada cPanel.

---

## 📦 Estrutura de Arquivos

```
aligator/
├── config.php              # Configurações globais, DB, helpers
├── index.php               # Home
├── sobre.php               # Página Sobre
├── solucoes.php            # Soluções
├── mentoria.php            # Mentoria
├── contato.php             # Contato + formulário de leads
├── pagina.php              # Renderizador de páginas CMS (criar)
├── sitemap.php             # Sitemap dinâmico
├── robots.txt              # Robots
├── llms.txt                # Arquivo para LLMs
├── .htaccess               # Rewrites, segurança, cache
├── migration.sql           # Script de migração do banco
│
├── includes/
│   ├── header.php          # Cabeçalho, nav, meta tags, schema
│   └── footer.php          # Rodapé, WhatsApp FAB, scripts
│
├── css/
│   └── style.css           # Design system completo
│
├── js/
│   └── main.js             # Scroll reveal, counters, forms
│
├── blog/
│   ├── index.php           # Listagem com paginação e categorias
│   └── post.php            # Post individual
│
├── ajax/
│   └── lead.php            # Endpoint AJAX para captura de leads
│
├── admin/
│   ├── index.php           # Dashboard com KPIs
│   ├── login.php           # Autenticação
│   ├── leads.php           # Gestão de leads + export CSV
│   ├── blog.php            # CRUD de posts com Quill editor
│   ├── configuracoes.php   # Configurações globais
│   └── includes/
│       └── auth.php        # Funções de autenticação
│
└── media/
    ├── uploads/            # Imagens de posts/banners (criar)
    ├── og-default.jpg      # Imagem Open Graph padrão (criar)
    └── logo.png            # Logo (criar)
```

---

## 🚀 Deploy — Passo a Passo

### 1. Banco de dados

```sql
-- 1. Execute o banco existente: bd_aligator.sql
-- 2. Execute o migration: migration.sql
-- (na ordem acima no phpMyAdmin ou MySQL CLI)
```

### 2. Configurar `config.php`

```php
define('DB_HOST', 'localhost');  // ou o host do seu plano
define('DB_NAME', 'bd_aligator');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');

define('SITE_URL',   'https://aligator.com.br');
define('SITE_EMAIL', 'contato@aligator.com.br');
define('SITE_WHATS', '5511999999999');  // DDI + DDD + número
```

### 3. Upload via cPanel / FTP

- Faça upload de todos os arquivos para `public_html/` (ou o Document Root configurado)
- Certifique-se que `.htaccess` foi enviado (arquivos ocultos precisam estar habilitados no FTP)
- Crie as pastas `media/uploads/` e `media/editor/` com permissão **755**

### 4. Criar primeiro admin

Execute no banco:
```sql
INSERT INTO sis_admins (sa_nivel, sa_nome, sa_email, sa_senha, sa_criacao)
VALUES (1, 'Seu Nome', 'admin@aligator.com.br', SHA1('sua_senha_aqui'), NOW());
```

### 5. Configurar Calendly

Acesse `/admin/configuracoes.php` e insira a URL do seu Calendly.

### 6. Configurar Google Tag Manager / Analytics

Insira os IDs em `/admin/configuracoes.php`. O site carregará automaticamente.

---

## 🔧 Configurações Extras

### Criar página `/pagina.php`

Para páginas CMS (política de privacidade, termos):

```php
<?php
require __DIR__ . '/config.php';
$slug = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
$page = db()->prepare('SELECT * FROM cms_paginas WHERE pag_slug = ? LIMIT 1');
$page->execute([$slug]);
$page = $page->fetch();
if (!$page) { http_response_code(404); exit; }
$seo_title = $page['pag_titulo'];
$seo_desc  = $page['pag_description'] ?? '';
require __DIR__ . '/includes/header.php';
echo '<div class="page-header"><div class="page-header-bg"></div>';
echo '<div class="container page-header-content"><h1>' . h($page['pag_titulo']) . '</h1></div></div>';
echo '<section><div class="container-s post-content">' . $page['pag_texto'] . '</div></section>';
require __DIR__ . '/includes/footer.php';
```

### Adicionar imagem Open Graph padrão

Crie `media/og-default.jpg` (1200×630px) com visual da marca.

### PHP compatibilidade

O sistema é compatível com **PHP 7.4+** e **PHP 8.x**.
Testado com MySQL 5.7+ e 8.0.

---

## 📊 Funcionalidades Entregues

| Funcionalidade                  | Status |
|---------------------------------|--------|
| Home com dashboard mockup       | ✅     |
| Página Sobre com timeline       | ✅     |
| Soluções (6 serviços)           | ✅     |
| Mentoria com formatos/preços    | ✅     |
| Blog (listagem + post)          | ✅     |
| Contato + lead form             | ✅     |
| Captura UTM automática          | ✅     |
| Admin: Dashboard                | ✅     |
| Admin: Leads + export CSV       | ✅     |
| Admin: Blog com Quill editor    | ✅     |
| Admin: Configurações            | ✅     |
| SEO: Meta tags dinâmicas        | ✅     |
| SEO: Schema.org                 | ✅     |
| SEO: Sitemap.xml dinâmico       | ✅     |
| SEO: robots.txt                 | ✅     |
| llms.txt                        | ✅     |
| Design system dark premium      | ✅     |
| WhatsApp FAB                    | ✅     |
| Mobile first / Responsivo       | ✅     |
| Scroll reveal animations        | ✅     |
| Counter animations              | ✅     |
| .htaccess segurança + cache     | ✅     |
| Rate limiting leads             | ✅     |
| Honeypot anti-spam              | ✅     |
| migration.sql                   | ✅     |

---

## 🎨 Design System

- **Fonte Display**: Syne (headings, logo)
- **Fonte Corpo**: Figtree (textos)
- **Cor primária**: `#00E87A` (verde)
- **Cor acento**: `#C8A44A` (ouro)
- **Background**: `#05080C` (preto profundo)
- **Surface**: `#101828`

---

## 📞 Suporte

Projeto desenvolvido pela Aligator.  
📧 contato@aligator.com.br
