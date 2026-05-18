# @luminix/mui-cms

O `@luminix/mui-cms` é a biblioteca de componentes React que fornece a interface visual do painel de administração. Construída com [Material-UI (MUI) v5](https://v5.mui.com/), oferece tabelas, filtros avançados, formulários, notificações e um sistema de extensibilidade via reducers.

> Documentação completa do pacote [`@luminix/mui-cms`](https://github.com/luminix-cms/js-mui-cms).

---

## Componente principal

### `<LuminixCms />`

O componente raiz que inicializa e renderiza o painel completo.

```jsx
import { LuminixCms } from '@luminix/mui-cms';

<LuminixCms
    theme={muiTheme}            // tema Material-UI (opcional)
    providers={[AppServiceProvider]}  // providers JavaScript
/>
```

**Props:**

| Prop | Tipo | Descrição |
|------|------|-----------|
| `theme` | `MuiTheme` | Tema Material-UI para customização visual |
| `themeArgs` | `array` | Argumentos adicionais para criação de tema |
| `providers` | `ServiceProvider[]` | Array de providers para extensão |

---

## Views

O painel possui quatro views principais, roteadas automaticamente pelo React Router:

| View | URL | Descrição |
|------|-----|-----------|
| `Dashboard` | `/admin` | Página inicial do painel |
| `ModelIndex` | `/admin/{modelo}` | Listagem paginada de registros |
| `ModelItem` | `/admin/{modelo}/{id}` | Visualização/edição de um registro |
| `Error` | `/admin/error` | Página de erro |

---

## Componentes

### Layout

- **`Sidebar`** — Navegação lateral gerada automaticamente do manifest de modelos
- **`Header`** — Cabeçalho com breadcrumbs e menu de usuário
- **`Breadcrumbs`** — Trilha de navegação contextual
- **`RecursiveMenu`** — Menu aninhado construído a partir do manifest

### ModelIndex (listagem)

| Componente | Função |
|-----------|--------|
| `Table` | Tabela de dados paginada |
| `Filter` | Painel de filtros avançados |
| `Pagination` | Navegação entre páginas |
| `Sort` | Ordenação por coluna |
| `Tabs` | Filtros rápidos por aba |
| `MassActions` | Ações em lote (excluir, restaurar) |
| `InstanceActions` | Ações por linha (editar, excluir) |
| `StaticActions` | Ações globais (criar novo registro) |

---

## Hooks

Hooks disponíveis para construção de componentes customizados:

| Hook | Descrição |
|------|-----------|
| `useCurrentModel()` | Retorna o modelo atualmente ativo |
| `useTable()` | Estado e ações da tabela (dados, loading, seleção) |
| `useSearch()` | Estado e controle da busca textual |
| `useNotify()` | Exibe notificações toast (sucesso, erro, info) |
| `useDialog()` | Abre dialogs de confirmação |
| `useMenu()` | Dados do menu de navegação |
| `useNotifications()` | Fila de notificações pendentes |
| `useActionEvent()` | Dispara ações programaticamente |
| `useBackButton()` | Navegação de volta |
| `useLayoutConfig()` | Configuração de tema e layout |
| `useHandleError()` | Tratamento padronizado de erros |
| `useKeyPress()` | Atalhos de teclado |

### Exemplo de uso

```jsx
import { useCurrentModel, useTable, useNotify } from '@luminix/mui-cms';

function MyComponent() {
    const model = useCurrentModel();
    const { data, loading } = useTable();
    const notify = useNotify();

    function handleAction() {
        notify.success('Ação executada com sucesso!');
    }

    return (
        <div>
            <h1>{model.displayName}</h1>
            {loading ? <p>Carregando...</p> : <p>{data.length} registros</p>}
            <button onClick={handleAction}>Executar</button>
        </div>
    );
}
```

---

## Sistema de filtros

A interface de filtros usa os mesmos operadores da API. O usuário pode adicionar múltiplas condições combinadas:

```
Título | contém | "laravel"
Data   | entre  | "2024-01-01" e "2024-12-31"
Status | igual  | "publicado"
```

Os filtros são sincronizados com a URL, permitindo compartilhar e bookmarkar buscas.

---

## Notificações e dialogs

### Notificações toast

```js
// Via hook
const notify = useNotify();
notify.success('Salvo com sucesso!');
notify.error('Erro ao salvar.');
notify.info('Operação em andamento...');
```

### Dialogs de confirmação

```js
const dialog = useDialog();

dialog.confirm({
    title: 'Excluir registro?',
    message: 'Esta ação não pode ser desfeita.',
    onConfirm: () => deleteRecord(id),
});
```

---

## Extensibilidade {#extensibilidade}

O painel expõe reducers para substituição ou extensão de qualquer parte da interface.

### Registrando reducers

Registre reducers no método `boot()` do `AppServiceProvider` JavaScript:

```js
import { ServiceProvider } from '@luminix/support';
import { CmsService } from '@luminix/mui-cms';

export default class AppServiceProvider extends ServiceProvider
{
    boot() {
        // Substituir componente de tabela
        CmsService.reducer('ModelIndexTable', () => MyCustomTable);

        // Adicionar ações a instâncias de linha
        CmsService.reducer('InstanceActions', (actions) => [
            ...actions,
            {
                label: 'Ver no site',
                icon: 'OpenInNew',
                onClick: (record) => window.open(`/posts/${record.slug}`),
            },
        ]);

        // Adicionar item ao menu lateral
        CmsService.reducer('menu', (menu) => [
            ...menu,
            { label: 'Relatórios', icon: 'BarChart', href: '/admin/reports' },
        ]);
    }
}
```

---

## Internacionalização

O painel usa [i18next](https://www.i18next.com/) para traduções. Os idiomas são configurados via `luminix/admin` e recebidos no boot data. Consulte [Internacionalização](internacionalizacao.md) para adicionar idiomas.

---

## Dependências

O pacote requer as seguintes *peer dependencies*:

| Pacote | Versão | Função |
|--------|--------|--------|
| `@luminix/core` | `^1.0.0` | Contêiner, modelos e roteamento |
| `@luminix/react` | `^1.0.0` | Hooks e componentes React base |
| `@luminix/support` | `^1.0.1` | Utilitários e padrão ServiceProvider |
| `@mui/material` | `^5.16.5` | Componentes Material-UI |
| `@mui/icons-material` | `^5.16.5` | Ícones Material-UI |
| `react` | `^18.3.1` | React |
| `react-dom` | `^18.3.1` | React DOM |
| `react-router-dom` | `6.25.1` | Roteamento SPA |
| `i18next` | `^23.12.2` | Internacionalização |
| `react-i18next` | `^15.0.1` | Bindings React para i18next |

> Ao usar o modo Vite com `luminix:admin-ui`, todas essas dependências são adicionadas automaticamente ao `package.json`.

---

## Build targets

| Comando | Saída | Uso |
|---------|-------|-----|
| `npm run build:bundle` | `bundle/mui-cms.iife.js` | CDN / unpkg (sem pipeline de build) |
| `npm run build:dist` | `dist/mui-cms.js` | Módulo ES para npm/Vite |

---

## Próximos passos

- Personalize o tema e os providers em [Personalização](personalizacao.md).
- Veja como os dados chegam do backend em [luminix/frontend](frontend.md).
- Configure novos idiomas em [Internacionalização](internacionalizacao.md).
- Consulte a [documentação completa do @luminix/mui-cms](https://github.com/luminix-cms/js-mui-cms) para referência detalhada de componentes, facades e tipos.
- Volte ao [índice](index.md).
