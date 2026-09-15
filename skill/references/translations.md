# Panel strings

Every label the panel renders is a Laravel **JSON** translation line, shipped in the `trans` boot
key and resolved by i18next. Keys are the English strings themselves, so `en` needs no file;
`lang/pt-BR.json` inside the package is both the bundled translation and the list of every key the
panel uses. Placeholders are Laravel-style (`:model`, `:label`, `:count`), not `{{ }}`.

## Picking the language

The strings shipped are those of `config('app.locale')`, resolved while the page is built. A
per-request `App::setLocale('es')` does not reach the panel — it reads the config value:

```php
// a middleware on the panel route, to follow the user's preference
config(['app.locale' => $user->locale]);
```

## Overriding a string

Add the key to the application's own `lang/{locale}.json`. That file is merged last, so it wins
over the package's line. There is no publish tag for this package's `lang/`:

```json
// lang/pt-BR.json
{ "Dashboard": "Início", "Users": "Clientes" }
```

Model names and column labels pass through the same table, which is why `"Users"` above renames the
drawer entry, the page title and the breadcrumb at once.

## Adding a language

1. create `lang/es.json` in the app, keyed by the English strings in the package's `lang/pt-BR.json`
2. set `config('app.locale')` to `es`
3. list `es` in `luminix.admin.locales` -> `configuration.md`

Untranslated keys fall through to the key itself, so a partial file degrades to English per line
rather than breaking the screen.
