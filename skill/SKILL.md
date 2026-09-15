---
name: luminix-admin
description: luminix/admin — Laravel host for the MUI admin panel. Install and first render, Vite vs CDN mode and the luminix:admin-ui command, the view-admin-panel gate and the session the API shares, the boot data the panel reads, panel translations, config keys. Read this before crawling vendor/luminix/admin/src.
allowed-tools: Read(.claude/skills/luminix-admin/**), Read(vendor/luminix/admin/**)
---

# `luminix/admin`

Laravel package that serves the `@luminix/mui-cms` admin panel: one catch-all route under a
configurable prefix, the page that boots the SPA, and the configuration that page carries with it.
No panel component, table or form lives here.

## Where to read

| Read this | When |
|---|---|
| `references/getting-started.md` | installing, the first render, why the drawer is empty or the panel shows nothing |
| `references/ui-modes.md` | `luminix:admin-ui`, CDN vs Vite, the published skeleton, Vite entry wiring, upgrading the bundle |
| `references/configuration.md` | `config/luminix/admin.php` — `url`, `middleware`, `locales` |
| `references/access-control.md` | `view-admin-panel`, what each denial returns, matching the API's auth middleware |
| `references/boot-data.md` | what this package injects into the frontend config, and adding keys of your own |
| `references/translations.md` | panel strings, overriding one, adding a language, picking the language |

## Owned elsewhere

- panel components, layout, tables, actions, reducers, the JS `AppServiceProvider` -> `@luminix/mui-cms`
- the REST endpoints the panel calls and their per-model `{action}-{alias}` gates -> `luminix/backend`
- the boot payload, the model manifest and the `@luminixEmbed()` directive -> `luminix/frontend`
