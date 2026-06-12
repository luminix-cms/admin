# Instalação

Este guia cobre a instalação completa do `luminix/admin` em uma aplicação Laravel existente.

## Requisitos

| Requisito | Versão suportada |
|-----------|-----------------|
| PHP | 8.2+ (8.3+ para Laravel 13) |
| Laravel | 11.x, 12.x ou 13.x |
| Node.js | 18+ (apenas modo Vite) |
| Composer | 2.x |

---

## Passo 1 — Instalar via Composer

```bash
composer require luminix/admin
```

O Laravel detecta automaticamente o `AdminServiceProvider` via *package discovery*. Os pacotes `luminix/backend` e `luminix/frontend` são instalados como dependências automaticamente.

---

## Passo 2 — Publicar os arquivos de UI

```bash
php artisan luminix:admin-ui
```

Este comando:

1. Consulta as *peer dependencies* do pacote npm `@luminix/mui-cms` no CDN unpkg
2. Adiciona essas dependências ao `package.json` do seu projeto (cria o arquivo se não existir)
3. Publica o skeleton de frontend via `vendor:publish`:
   - `resources/views/vendor/admin/cms.blade.php` — view Blade com suporte a Vite
   - `resources/js/luminix-admin.jsx` — entry point React
   - `resources/js/Providers/AppServiceProvider.js` — provider de customização

Use `--force` para sobrescrever arquivos existentes:

```bash
php artisan luminix:admin-ui --force
```

---

## Passo 3 — Configurar o Vite

Abra o `vite.config.js` da sua aplicação e adicione o entry point publicado:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/luminix-admin.jsx', // ← adicione esta linha
            ],
            refresh: true,
        }),
        react(),
    ],
});
```

---

## Passo 4 — Instalar dependências npm e compilar

```bash
npm install
npm run dev     # desenvolvimento com HMR
# ou
npm run build   # build de produção
```

---

## Passo 5 — Definir a autorização

O painel exige que o usuário autenticado possua a *ability* `view-admin-panel`. Registre-a no método `boot()` do `AppServiceProvider` da sua aplicação:

```php
// app/Providers/AppServiceProvider.php
use Illuminate\Support\Facades\Gate;

public function boot(): void
{
    Gate::define('view-admin-panel', function ($user) {
        return $user->is_admin; // adapte à sua lógica
    });
}
```

---

## Passo 6 — Publicar a configuração (opcional)

Para customizar as opções do painel (URL, middleware, locales), publique o arquivo de configuração:

```bash
php artisan vendor:publish --provider="Luminix\Admin\AdminServiceProvider" --tag="luminix-config"
```

Isso cria `config/luminix/admin.php`. Consulte [Configuração](configuracao.md) para todos os detalhes.

---

## Verificando a instalação

Acesse `/admin` no navegador. Se tudo estiver correto, o painel será exibido após o login. Se o usuário não possuir a *ability* `view-admin-panel`, o Laravel retornará um erro 403.

---

## Modo CDN (sem build)

Caso não queira configurar um pipeline de build, o painel funciona diretamente com os bundles pré-compilados do CDN. Basta não executar os passos 2 a 4 e o Laravel usará a view padrão do pacote, que carrega os assets do [unpkg.com](https://unpkg.com) automaticamente.

> Este modo não permite personalização do frontend. Para customizar componentes, temas ou providers, use o modo Vite.

---

## Expondo modelos na API

Após instalar o pacote, adicione a trait `LuminixModel` aos modelos que devem aparecer no painel:

```php
// app/Models/Post.php
use Luminix\Backend\Model\LuminixModel;

class Post extends Model
{
    use LuminixModel;

    protected $fillable = ['title', 'body', 'published_at'];
}
```

Isso gera automaticamente os endpoints REST em `/luminix-api/posts` e faz o modelo aparecer na navegação lateral do painel. Consulte [luminix/backend](backend.md) para a documentação completa.

---

## Próximos passos

- Veja todas as opções de configuração em [Configuração](configuracao.md).
- Configure autenticação e permissões em [Autorização](autorizacao.md).
- Aprenda a expor modelos Eloquent em [luminix/backend](backend.md).
- Personalize o painel em [Personalização](personalizacao.md).
