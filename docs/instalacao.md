# Instalação

Este guia cobre a instalação completa do `luminix/admin` em uma aplicação Laravel existente.

## Requisitos

| Requisito | Versão suportada |
|-----------|-----------------|
| PHP | 8.2+ (8.3+ para Laravel 13) |
| Laravel | 11.x, 12.x ou 13.x |
| Node.js | 20+ (apenas modo Vite) |
| Composer | 2.x |

O painel usa o React Router 7, que exige Node.js 20 ou mais novo no ambiente onde roda `npm install` e `npm run build`. O [modo CDN](#modo-cdn-sem-build) dispensa o Node.js.

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

1. Monta a lista de dependências npm do painel: `@luminix/mui-cms` 2.x e as *peer dependencies* nas versões que ele exige — React 18, Material-UI 5 e React Router 7, entre outras (veja a [tabela completa](js-mui-cms.md#dependências)). A lista vem embutida no `luminix/admin`; o comando não consulta o npm nem o unpkg
2. Adiciona essas dependências ao `package.json` do seu projeto (todo projeto Laravel novo traz um), trocando a versão dos pacotes que já estiverem lá
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

> Use o Node.js 20 ou mais novo (veja os [requisitos](#requisitos)).

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

## Atualizando da 1.x para a 2.x

A 2.0 entrega o painel `@luminix/mui-cms` 2.0, que troca o React Router 6 pelo **React Router 7**. Quem usa o `luminix/admin` 1.x precisa subir a restrição no Composer; quem usa o modo Vite também regrava o `package.json` e reinstala as dependências npm.

### Modo CDN

Basta atualizar o pacote. A URL do unpkg passa a apontar para a 2.0.0 e não há mais nada a fazer:

```bash
composer require luminix/admin:^2.0
```

### Modo Vite

1. Confirme que o ambiente de build usa **Node.js 20 ou mais novo**, o mínimo do React Router 7.
2. Atualize o pacote, regrave o `package.json` e reinstale as dependências:

   ```bash
   composer require luminix/admin:^2.0
   php artisan luminix:admin-ui
   npm install
   ```

   Confirme a lista que o comando mostra. Ele leva `@luminix/mui-cms` para `^2.0.0` e `react-router-dom` — que a 1.x fixava em `6.25.1` — para `^7.18.0`, e sobe `@luminix/core`, `@luminix/react` e `@luminix/support` para as versões que o painel 2.0 exige. As demais dependências do seu projeto ficam como estão, e os arquivos que você já publicou (`resources/js/luminix-admin.jsx`, `resources/js/Providers/AppServiceProvider.js` e a view) também: a 2.0 não muda o skeleton.

   > Rode sem o `--force`: ele pula a confirmação, mas também sobrescreve esses arquivos publicados, e com eles qualquer personalização sua.

3. Revise o código do seu projeto que usa o React Router diretamente. Os imports de `react-router-dom` continuam valendo na 7, mas o React Router 7 não instala mais o `@remix-run/router`: imports diretos dele passam a vir de `react-router`. O [guia de atualização do React Router](https://reactrouter.com/7.18.0/upgrading/v6) lista as demais mudanças.
4. Plugins do ecossistema que declaram `react-router-dom` 6 ou `@luminix/mui-cms` 1.x como *peer dependency* fazem o `npm install` falhar com `ERESOLVE` até ganharem uma versão compatível com a 2.x.

### Ficando na 1.x

Quem não pode migrar para o React Router 7 permanece em `luminix/admin` `^1.0`, que usa `@luminix/mui-cms` 1.x com o React Router 6 e é mantido na branch `v1.x`.

---

## Próximos passos

- Veja todas as opções de configuração em [Configuração](configuracao.md).
- Configure autenticação e permissões em [Autorização](autorizacao.md).
- Aprenda a expor modelos Eloquent em [luminix/backend](backend.md).
- Personalize o painel em [Personalização](personalizacao.md).
