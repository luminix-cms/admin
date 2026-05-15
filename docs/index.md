# Documentação — luminix/admin

`luminix/admin` é o ponto de entrada do ecossistema Luminix: um pacote Laravel que instala, configura e serve um painel de administração CMS completo, construído com React e Material-UI.

---

## Sumário

| Página | Descrição |
|--------|-----------|
| [Introdução](introducao.md) | O que é o Luminix, arquitetura do ecossistema e fluxo de dados |
| [Instalação](instalacao.md) | Guia passo a passo para instalar e colocar o painel no ar |
| [Configuração](configuracao.md) | Todas as opções do arquivo `config/luminix/admin.php` |
| [Autorização](autorizacao.md) | Gates, middlewares e controle de acesso por linha |
| [Personalização](personalizacao.md) | `AppServiceProvider`, tema Material-UI e extensão via providers |
| [Internacionalização](internacionalizacao.md) | Suporte a múltiplos idiomas e como adicionar novas traduções |
| [luminix/backend](backend.md) | API REST automática: modelos, filtros, segurança e eventos |
| [luminix/frontend](frontend.md) | Injeção de boot data, manifest e reducers via Blade |
| [@luminix/mui-cms](js-mui-cms.md) | Componentes React, hooks e sistema de extensibilidade |

---

## Visão geral do ecossistema

O Luminix é composto por quatro pacotes que trabalham juntos:

```
luminix/admin        ← você está aqui (ponto de entrada)
    ├── luminix/backend   API REST automática (Laravel)
    ├── luminix/frontend  Injeção de dados de boot (Laravel)
    └── @luminix/mui-cms  Componentes do painel (React + MUI)
```

Instalar `luminix/admin` é suficiente para trazer todos os pacotes PHP necessários. O comando `php artisan luminix:admin-ui` cuida das dependências JavaScript.

---

## Próximos passos

- Novo por aqui? Comece pela [Introdução](introducao.md) para entender a arquitetura.
- Pronto para instalar? Siga o [Guia de Instalação](instalacao.md).
- Quer personalizar o painel? Veja [Personalização](personalizacao.md).
