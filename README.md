# luminix/admin

Painel de administração CMS para aplicações Laravel, construído com React e Material-UI. Parte do ecossistema [Luminix](https://github.com/luminix-cms).

---

## Sumário

- [Requisitos](#requisitos)
- [Instalação](#instalação)
- [Configuração](#configuração)
- [Uso](#uso)
- [Comandos Artisan](#comandos-artisan)
- [Arquitetura](#arquitetura)
- [Personalização do Frontend](#personalização-do-frontend)
- [Internacionalização](#internacionalização)
- [Desenvolvimento do pacote](#desenvolvimento-do-pacote)

---

## Requisitos

- PHP 8.2+
- Laravel 11.x
- Node.js (para o modo Vite)
- Pacotes do ecossistema Luminix:
  - `luminix/frontend` (1.x-dev)
  - `luminix/backend` (recomendado, necessário para operações de modelo)

---

## Instalação

### 1. Instalar via Composer

```bash
composer require luminix/admin
```

O Laravel detecta automaticamente o `AdminServiceProvider` via *package discovery*.

### 2. Publicar a UI (modo Vite — recomendado)

```bash
php artisan luminix:admin-ui
```

Este comando:
- Adiciona as dependências npm necessárias ao `package.json` do projeto
- Publica os arquivos skeleton (`resources/views/vendor/admin/cms.blade.php` e `resources/js/`)

### 3. Configurar o Vite

Adicione o entry point publicado ao `vite.config.js`:

```js
export default defineConfig({
    plugins: [
        laravel({
            input: [
                // outros entry points...
                "resources/js/luminix-admin.jsx",
            ],
            refresh: true,
        }),
        react(),
    ],
});
```

### 4. Instalar dependências e compilar

```bash
npm install && npm run dev
```

### 5. Autorização

O painel exige que o usuário autenticado possua a *ability* `view-admin-panel`. Registre-a no `AuthServiceProvider` da sua aplicação:

```php
use Illuminate\Support\Facades\Gate;

Gate::define('view-admin-panel', function ($user) {
    return $user->is_admin; // sua lógica de autorização
});
```

---

## Configuração

### Publicar o arquivo de configuração

```bash
php artisan vendor:publish --provider="Luminix\Admin\AdminServiceProvider" --tag="luminix-config"
```

Isso cria `config/luminix/admin.php`:

```php
return [

    // URL base do painel. Pode ser sobrescrita via variável de ambiente.
    'url' => env('LUMINIX_ADMIN_URL', '/admin'),

    // Middlewares aplicados às rotas do painel.
    'middleware' => ['web', 'auth', 'can:view-admin-panel'],

    // Locales suportados pelo painel.
    'locales' => ['en', 'pt-BR'],

];
```

### Variáveis de ambiente

| Variável              | Padrão   | Descrição                    |
|-----------------------|----------|------------------------------|
| `LUMINIX_ADMIN_URL`   | `/admin` | Prefixo de URL do painel     |

---

## Uso

Após a instalação, o painel estará disponível em `/admin` (ou no URL configurado). O acesso exige:

1. Usuário autenticado (`auth` middleware)
2. Permissão `view-admin-panel` (Gate do Laravel)

O painel é uma SPA (Single Page Application): todas as sub-rotas de `/admin/*` são capturadas e roteadas pelo React no frontend.

---

## Comandos Artisan

### `php artisan luminix:admin-ui`

Publica os arquivos de UI (frontend) para a aplicação.

**O que faz:**
1. Consulta as *peer dependencies* do pacote no unpkg CDN
2. Adiciona/atualiza as dependências no `package.json` do projeto
3. Move dependências que estiverem em `devDependencies` para `dependencies`, se necessário
4. Publica os arquivos skeleton via `vendor:publish`
5. Exibe instruções para configurar o Vite

**Opções:**

| Opção     | Descrição                                          |
|-----------|----------------------------------------------------|
| `--force` | Sobrescreve arquivos existentes sem confirmação    |

**Exemplo:**

```bash
php artisan luminix:admin-ui --force
```

---

## Arquitetura

### Estrutura do pacote

```
luminix/admin/
├── src/
│   ├── AdminServiceProvider.php       # Entrada do pacote
│   ├── Http/
│   │   └── Controllers/
│   │       └── CmsController.php      # Renderiza a view do painel
│   ├── Console/
│   │   └── Commands/
│   │       └── UiCommand.php          # Comando luminix:admin-ui
│   └── Support/
│       └── Unpkg.php                  # Helper para URLs do CDN
├── routes/
│   └── web.php                        # Rota catch-all do painel
├── config/
│   └── admin.php                      # Configuração padrão
├── resources/
│   └── views/
│       └── cms.blade.php              # Template CDN (sem build)
├── skeleton/
│   ├── views/
│   │   └── cms.blade.php              # Template Vite (publicado)
│   └── js/
│       ├── luminix-admin.jsx          # Entry point React
│       └── Providers/
│           └── AppServiceProvider.js  # Provider de customização
└── lang/
    └── pt-BR.json                     # Traduções em português
```

### Fluxo de requisição

```
Requisição HTTP /admin/*
        │
        ▼
   Middleware Stack
   (web, auth, can:view-admin-panel)
        │
        ▼
   CmsController::render()
        │
        ▼
   View admin::cms (Blade)
        │
        ▼
   @luminixEmbed()  ←── Injeta configuração da aplicação (JSON)
        │
        ▼
   React SPA (LuminixCms)
```

### Modos de renderização

#### Modo CDN (padrão, sem build)

A view em `resources/views/cms.blade.php` carrega os bundles pré-compilados diretamente do [unpkg](https://unpkg.com):

```
https://unpkg.com/@luminix/mui-cms@{CMS_VERSION}/bundle/mui-cms.bundle.iife.js
https://unpkg.com/@luminix/mui-cms@{CMS_VERSION}/bundle/style.css
```

Indicado para uso rápido sem pipeline de build.

#### Modo Vite (recomendado para produção)

Após executar `luminix:admin-ui`, a view publicada em `resources/views/vendor/admin/cms.blade.php` usa o Vite para compilar o frontend localmente, com suporte a HMR em desenvolvimento.

### AdminServiceProvider

O `AdminServiceProvider` é responsável por:

- Registrar o comando `luminix:admin-ui`
- Mesclar configurações padrão (`luminix.admin`)
- Publicar configuração e arquivos de UI
- Carregar rotas, views e traduções
- Injetar configuração no frontend via `BootService::reducer('wireConfig', ...)`:
  - Traduções do locale ativo
  - URL do painel
  - Locales suportados
  - Operadores de filtro disponíveis (`ModelFilter::operators()`)
  - Campos excluídos do filtro

### Roteamento

```php
// routes/web.php
Route::group([
    'middleware' => config('luminix.admin.middleware'),
    'prefix' => config('luminix.admin.url'),
], function () {
    Route::get('/{splat?}', [CmsController::class, 'render'])->where('splat', '.+');
});
```

Uma única rota catch-all captura todos os caminhos abaixo do prefixo configurado, delegando a navegação ao React Router no frontend.

---

## Personalização do Frontend

Após publicar a UI com `luminix:admin-ui`, o arquivo `resources/js/Providers/AppServiceProvider.js` permite estender o comportamento do painel:

```js
import { ServiceProvider } from '@luminix/support';

export default class AppServiceProvider extends ServiceProvider
{
    register() {
        // Registre bindings, configurações, etc.
    }

    boot() {
        // Execute código após todos os providers serem registrados.
    }
}
```

Esse provider é passado ao componente `LuminixCms` em `resources/js/luminix-admin.jsx`:

```jsx
<LuminixCms
    providers={[
        AppServiceProvider,
        // Adicione mais providers aqui
    ]}
/>
```

Para mais informações sobre como personalizar, consulte a [documentação do Luminix MUI CMS](https://github.com/luminix-cms/js-mui-cms) no GitHub.

---

## Internacionalização

O painel suporta os idiomas configurados em `luminix.admin.locales` (padrão: `en` e `pt-BR`).

### Traduções incluídas

O pacote inclui traduções em **Português Brasileiro** (`lang/pt-BR.json`) com strings para:

- Rótulos de interface (Dashboard, Criar, Editar, Excluir, Restaurar...)
- Operadores de filtro (Contém, Inicia com, Entre, Nulo...)
- Diálogos de confirmação
- Mensagens de sucesso/erro
- Strings com placeholders dinâmicos (`:model`, `:label`, `:count`)

### Adicionando novos idiomas

1. Crie o arquivo de tradução em `lang/{locale}.json` na sua aplicação
2. Adicione o locale à configuração:

```php
// config/luminix/admin.php
'locales' => ['en', 'pt-BR', 'es'],
```

As traduções são carregadas automaticamente pelo `AdminServiceProvider` e enviadas ao frontend via `BootService`.

---

## Desenvolvimento do pacote

### Pré-requisitos

- PHP 8.2+
- Composer

### Setup

```bash
git clone https://github.com/luminix-cms/admin.git
cd admin
composer install
```

### Executar os testes

```bash
composer test
```

Para contribuir, faça um pull request ou envie uma issue no repositório.

---

## Licença

MIT — veja o arquivo [LICENSE](LICENSE) para detalhes.
