# Personalização

Após publicar os arquivos de UI com `php artisan luminix:admin-ui`, você tem controle total sobre o frontend do painel. A personalização é feita principalmente através de **Service Providers** e da **prop `theme`** do componente `LuminixCms`.

---

## AppServiceProvider

O arquivo `resources/js/Providers/AppServiceProvider.js` é o ponto central de extensão do painel. Ele é um Service Provider JavaScript que segue o mesmo padrão do `AppServiceProvider` do Laravel.

```js
// resources/js/Providers/AppServiceProvider.js
import { ServiceProvider } from '@luminix/support';

export default class AppServiceProvider extends ServiceProvider
{
    register() {
        // Registre bindings e configurações aqui.
        // Este método é chamado antes do boot de qualquer provider.
    }

    boot() {
        // Execute código após todos os providers serem registrados.
        // É o lugar correto para interagir com serviços já inicializados.
    }
}
```

Este provider é passado ao componente `LuminixCms` em `resources/js/luminix-admin.jsx`:

```jsx
// resources/js/luminix-admin.jsx
import { LuminixCms } from '@luminix/mui-cms';
import AppServiceProvider from './Providers/AppServiceProvider';

ReactDOM.createRoot(document.getElementById('root')).render(
    <React.StrictMode>
        <LuminixCms
            providers={[
                AppServiceProvider,
                // Adicione mais providers aqui
            ]}
        />
    </React.StrictMode>
);
```

---

## Tema Material-UI

Personalize as cores, tipografia e outros aspectos visuais do painel passando um tema ao componente `LuminixCms`:

```jsx
// resources/js/luminix-admin.jsx
import { createTheme } from '@mui/material/styles';

const theme = createTheme({
    palette: {
        primary: {
            main: '#1d9798',
        },
        secondary: {
            main: '#fa510c',
        },
    },
    typography: {
        fontFamily: '"Inter", "Roboto", "Helvetica", "Arial", sans-serif',
    },
});

ReactDOM.createRoot(document.getElementById('root')).render(
    <React.StrictMode>
        <LuminixCms
            theme={theme}
            providers={[AppServiceProvider]}
        />
    </React.StrictMode>
);
```

Consulte a [documentação do Material-UI](https://mui.com/material-ui/customization/theming/) para todas as opções de tema disponíveis.

---

## Múltiplos providers

Você pode organizar a customização em múltiplos providers e passá-los em ordem:

```jsx
import AppServiceProvider from './Providers/AppServiceProvider';
import ThemeServiceProvider from './Providers/ThemeServiceProvider';
import MenuServiceProvider from './Providers/MenuServiceProvider';

<LuminixCms
    providers={[
        AppServiceProvider,   // registrado primeiro
        ThemeServiceProvider,
        MenuServiceProvider,  // registrado por último
    ]}
/>
```

Os providers são registrados e inicializados na ordem em que aparecem no array.

---

## Customizando via Reducers (PHP)

No lado do servidor, o `AdminServiceProvider` usa o sistema de reducers do `luminix/frontend` para injetar configurações no frontend. Você pode interceptar e modificar esses dados no `AppServiceProvider` da sua aplicação Laravel:

```php
// app/Providers/AppServiceProvider.php
use Luminix\Frontend\Services\BootService;

public function boot(): void
{
    BootService::reducer('wireConfig', function (array $boot) {
        return [
            ...$boot,
            'app' => [
                ...$boot['app'],
                'version' => config('app.version'),
                'support_email' => config('app.support_email'),
            ],
        ];
    });
}
```

Esses dados estarão disponíveis no frontend via a instância do app Luminix. Consulte [luminix/frontend](frontend.md#reducers) para a documentação completa dos reducers.

---

## Customizando componentes (Reducers JavaScript)

O `@luminix/mui-cms` expõe um sistema de reducers para substituir ou estender componentes e comportamentos do painel. Registre reducers no método `boot()` do `AppServiceProvider` JavaScript:

```js
import { ServiceProvider } from '@luminix/support';
import { CmsService } from '@luminix/mui-cms';
import MyCustomTable from '../components/MyCustomTable';

export default class AppServiceProvider extends ServiceProvider
{
    boot() {
        // Substituir o componente de tabela padrão
        CmsService.reducer('ModelIndexTable', () => MyCustomTable);
    }
}
```

Consulte a documentação do [@luminix/mui-cms](js-mui-cms.md#extensibilidade) para a lista completa de reducers disponíveis.

---

## Customizando menus e navegação

A navegação lateral é gerada automaticamente a partir do manifest de modelos. Para personalizar o menu, use o reducer `menu` no `AppServiceProvider` JavaScript:

```js
import { ServiceProvider } from '@luminix/support';
import { CmsService } from '@luminix/mui-cms';

export default class AppServiceProvider extends ServiceProvider
{
    boot() {
        CmsService.reducer('menu', (menu) => [
            ...menu,
            {
                label: 'Relatórios',
                icon: 'BarChart',
                href: '/admin/reports',
            },
        ]);
    }
}
```

---

## Estrutura do skeleton publicado

```
resources/js/
├── luminix-admin.jsx           # Entry point — edite para mudar providers e tema
└── Providers/
    └── AppServiceProvider.js   # Provider principal — registre customizações aqui
```

```
resources/views/vendor/admin/
└── cms.blade.php               # View Blade — edite para modificar o HTML base
```

---

## Próximos passos

- Adicione ou modifique idiomas em [Internacionalização](internacionalizacao.md).
- Aprenda sobre componentes e hooks disponíveis em [@luminix/mui-cms](js-mui-cms.md).
- Entenda como os dados chegam ao frontend em [luminix/frontend](frontend.md).
- Volte ao [índice](index.md) para ver todos os tópicos.
