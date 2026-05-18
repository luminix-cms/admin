# Configuração

## Publicando o arquivo de configuração

```bash
php artisan vendor:publish --provider="Luminix\Admin\AdminServiceProvider" --tag="luminix-config"
```

Isso cria o arquivo `config/luminix/admin.php` na sua aplicação.

---

## Opções disponíveis

```php
// config/luminix/admin.php

return [

    // URL base do painel de administração.
    'url' => env('LUMINIX_ADMIN_URL', '/admin'),

    // Middlewares aplicados às rotas do painel.
    'middleware' => ['web', 'auth', 'can:view-admin-panel'],

    // Locales suportados pela interface do painel.
    'locales' => ['en', 'pt-BR'],

];
```

---

## Referência das opções

### `url`

**Tipo:** `string`  
**Padrão:** `/admin`  
**Variável de ambiente:** `LUMINIX_ADMIN_URL`

Define o prefixo de URL do painel de administração. Todas as rotas do painel ficam sob esse caminho.

```php
'url' => env('LUMINIX_ADMIN_URL', '/painel'),
```

```env
# .env
LUMINIX_ADMIN_URL=/painel
```

> O Laravel registra uma rota catch-all `/{splat?}` sob esse prefixo. Qualquer caminho abaixo dele (ex: `/admin/posts`, `/admin/users/42`) é roteado para o React SPA.

---

### `middleware`

**Tipo:** `array`  
**Padrão:** `['web', 'auth', 'can:view-admin-panel']`

Lista de middlewares aplicados a **todas** as rotas do painel. Os middlewares são executados na ordem em que aparecem.

```php
'middleware' => ['web', 'auth', 'can:view-admin-panel'],
```

**Middlewares padrão:**

| Middleware | Função |
|-----------|--------|
| `web` | Sessão, cookies, CSRF e proteções HTTP padrão do Laravel |
| `auth` | Exige usuário autenticado. Redireciona para login se não autenticado |
| `can:view-admin-panel` | Verifica a *ability* `view-admin-panel` via Laravel Gates |

**Exemplos de customização:**

```php
// Adicionar verificação de e-mail
'middleware' => ['web', 'auth', 'verified', 'can:view-admin-panel'],

// Usar um middleware personalizado
'middleware' => ['web', 'auth', 'admin', 'can:view-admin-panel'],

// Remover a verificação por Gate (não recomendado em produção)
'middleware' => ['web', 'auth'],
```

> Para configurar middlewares da **API REST** (`/luminix-api/*`), consulte a [configuração do luminix/backend](backend.md#configuração).

---

### `locales`

**Tipo:** `array`  
**Padrão:** `['en', 'pt-BR']`

Lista de idiomas suportados pela interface do painel. O locale ativo (definido pela configuração `app.locale` do Laravel) determina qual tradução é carregada.

```php
'locales' => ['en', 'pt-BR', 'es'],
```

O `AdminServiceProvider` carrega automaticamente as traduções do locale ativo e as envia ao frontend via `BootService`. Consulte [Internacionalização](internacionalizacao.md) para adicionar novos idiomas.

---

## Variáveis de ambiente

| Variável | Padrão | Descrição |
|----------|--------|-----------|
| `LUMINIX_ADMIN_URL` | `/admin` | Prefixo de URL do painel |

---

## Configurações das bibliotecas complementares

O ecossistema Luminix possui configurações adicionais nos pacotes complementares. Publique-as com:

```bash
# Configurações do backend (API REST)
php artisan vendor:publish --provider="Luminix\Backend\BackendServiceProvider" --tag="luminix-config"

# Configurações do frontend (boot data)
php artisan vendor:publish --provider="Luminix\Frontend\FrontendServiceProvider" --tag="luminix-config"
```

Consulte as páginas de documentação de cada pacote para os detalhes:
- [luminix/backend — Configuração](backend.md#configuração)
- [luminix/frontend — Configuração](frontend.md#configuração)

---

## Próximos passos

- Configure o controle de acesso em [Autorização](autorizacao.md).
- Adicione novos idiomas em [Internacionalização](internacionalizacao.md).
- Veja as opções de API em [luminix/backend](backend.md).
- Personalize a injeção de dados em [luminix/frontend](frontend.md).
