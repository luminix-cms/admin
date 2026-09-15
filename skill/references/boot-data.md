# Boot data the panel reads

The panel takes no props from PHP. Everything it learns about the application arrives in the JSON
that `@luminixEmbed()` writes into the page, built by `luminix/frontend` and extended by this
package through a `wireConfig` reducer.

## What this package adds

| Key | Value |
|---|---|
| `trans` | every JSON translation line of `config('app.locale')` -> `translations.md` |
| `luminix.admin.url` | the configured prefix; the panel uses it as its router basename |
| `luminix.admin.locales` | the configured list, verbatim |
| `luminix.admin.filter.operators` | `ModelFilter::operators()` — the built-in operators plus every macro registered on `luminix/backend`, so a custom operator appears in the panel's filter dropdown without frontend work |
| `luminix.admin.filter.exclude` | `luminix.backend.api.filter.exclude`, verbatim |

The panel's own components consume `url` and `filter.operators`; `locales` and `filter.exclude` are
exposed for client code to read.

Everything else in the payload — `app`, `auth`, `manifest` — is `luminix/frontend`'s.

## Adding keys of your own

```php
// AppServiceProvider::register()
use Luminix\Frontend\Services\BootService;

BootService::reducer('wireConfig', fn (array $config) => [
    ...$config,
    'luminix' => [
        ...$config['luminix'] ?? [],
        'admin' => [...$config['luminix']['admin'] ?? [], 'myFlag' => true],
    ],
]);
```

Spread the existing `luminix` block as above. This package's own reducer does the same, so the two
compose in either registration order; overwriting the block instead drops `url`, `locales` and the
filter data and breaks panel routing.

Read it back in the panel with `Config.get('luminix.admin.myFlag')` -> `@luminix/core`.
