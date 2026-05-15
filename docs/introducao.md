# Introdução ao Luminix

O **Luminix** é um ecossistema de pacotes para construção de painéis de administração CMS em aplicações Laravel. Ele conecta uma API REST automática no backend a uma interface React + Material-UI no frontend, com o mínimo de configuração manual.

O `luminix/admin` é o **ponto de entrada** do ecossistema: instala as dependências PHP, publica o skeleton do frontend e registra as rotas do painel.

---

## O ecossistema Luminix

O ecossistema é composto por quatro pacotes com responsabilidades distintas:

| Pacote | Tipo | Responsabilidade |
|--------|------|-----------------|
| `luminix/admin` | Laravel | Ponto de entrada: rotas, view Blade, comandos Artisan |
| `luminix/backend` | Laravel | Geração automática de endpoints REST a partir de modelos Eloquent |
| `luminix/frontend` | Laravel | Injeção de dados de inicialização (`boot data`) via diretiva Blade |
| `@luminix/mui-cms` | React/npm | Componentes do painel: tabelas, filtros, formulários, notificações |

### Dependências internas

```
luminix/admin
    ├── luminix/frontend   (requer)
    │       └── luminix/backend  (requer)
    └── @luminix/mui-cms   (dependência npm, publicada via luminix:admin-ui)
```

Instalar `luminix/admin` via Composer já traz `luminix/frontend` e `luminix/backend` automaticamente. O comando `php artisan luminix:admin-ui` cuida das dependências JavaScript.

---

## Arquitetura

### Visão geral

```
┌─────────────────────────────────────────────────────────┐
│                    Navegador do Usuário                  │
│                                                           │
│  ┌────────────────────────────────────────────────────┐  │
│  │  React SPA (@luminix/mui-cms)                     │  │
│  │  ├─ Tabelas, filtros, formulários, notificações  │  │
│  │  ├─ Contextos de estado (Model, Table, Filter)   │  │
│  │  └─ Hooks (useTable, useDialog, useNotify)       │  │
│  └────────────────────────────────────────────────────┘  │
└───────────────────────┬──────────────────────────────────┘
                        │ Requisições HTTP/JSON
                        ▼
┌─────────────────────────────────────────────────────────┐
│           Aplicação Laravel                              │
│                                                           │
│  ┌────────────────────────────────────────────────────┐  │
│  │ luminix/admin                                     │  │
│  │  Rota catch-all → CmsController → View Blade     │  │
│  └────────────────────┬───────────────────────────────┘  │
│                       │                                   │
│  ┌────────────────────────────────────────────────────┐  │
│  │ luminix/frontend                                  │  │
│  │  @luminixEmbed() → JSON com app + auth + manifest│  │
│  └────────────────────┬───────────────────────────────┘  │
│                       │                                   │
│  ┌────────────────────────────────────────────────────┐  │
│  │ luminix/backend                                   │  │
│  │  /luminix-api/{model} → CRUD + filtros + Gates   │  │
│  └────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────┘
```

### Fluxo de uma requisição

1. **Carregamento inicial**
   - O usuário acessa `/admin`
   - O Laravel valida autenticação e a *ability* `view-admin-panel`
   - O `CmsController` renderiza a view Blade
   - A diretiva `@luminixEmbed()` injeta um JSON oculto com dados da aplicação, do usuário autenticado e do manifest de modelos/rotas

2. **Listagem de registros**
   - O React SPA lê os dados injetados e inicializa
   - O usuário acessa um modelo → o React busca `GET /luminix-api/posts?page=1`
   - O `luminix/backend` aplica filtros, verifica Gates e retorna JSON paginado
   - A tabela renderiza os dados

3. **Criação de um registro**
   - O usuário preenche o formulário e confirma
   - O React envia `POST /luminix-api/posts` com os dados
   - O backend valida, dispara o evento `luminixCreating`, salva e dispara `luminixSaved`
   - O frontend exibe notificação de sucesso e atualiza a tabela

---

## Modos de renderização

### Modo CDN (sem pipeline de build)

A view padrão do pacote carrega os bundles pré-compilados do [unpkg.com](https://unpkg.com). Indicado para protótipos ou aplicações sem pipeline de build configurado.

### Modo Vite (recomendado para produção)

Após executar `php artisan luminix:admin-ui`, a view publicada usa o Vite para compilar o frontend localmente. Permite HMR em desenvolvimento, otimizações de bundle em produção e total controle sobre o código do frontend.

---

## Estrutura do pacote

```
luminix/admin/
├── src/
│   ├── AdminServiceProvider.php        # Entrada do pacote (registro e boot)
│   ├── Http/Controllers/
│   │   └── CmsController.php           # Renderiza a view do painel
│   ├── Console/Commands/
│   │   └── UiCommand.php               # php artisan luminix:admin-ui
│   └── Support/
│       └── Unpkg.php                   # Helper para URLs do CDN unpkg
├── routes/
│   └── web.php                         # Rota catch-all do painel
├── config/
│   └── admin.php                       # Configuração padrão
├── resources/views/
│   └── cms.blade.php                   # View CDN (sem build)
├── skeleton/
│   ├── views/cms.blade.php             # View Vite (publicada no projeto)
│   └── js/
│       ├── luminix-admin.jsx           # Entry point React
│       └── Providers/AppServiceProvider.js
└── lang/
    └── pt-BR.json                      # Traduções em português
```

---

## Próximos passos

- Pronto para instalar? Siga o [Guia de Instalação](instalacao.md).
- Entenda todas as opções disponíveis em [Configuração](configuracao.md).
- Saiba como controlar o acesso em [Autorização](autorizacao.md).
- Conheça a API REST gerada pelo [luminix/backend](backend.md).
