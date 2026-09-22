# Configuration — `config/luminix/admin.php`

Publish it with `php artisan vendor:publish --tag=luminix-config`; the tag is shared, so every
installed `luminix/*` package writes its config in the same run.

```php
return [
    'url'        => env('LUMINIX_ADMIN_URL', '/admin'),
    'middleware' => ['web', 'auth', 'can:view-admin-panel'],
    'locales'    => ['en', 'pt-BR'],
    'brand'      => [
        'name'      => env('LUMINIX_ADMIN_BRAND_NAME'),
        'logo'      => env('LUMINIX_ADMIN_BRAND_LOGO'),
        'logo_dark' => env('LUMINIX_ADMIN_BRAND_LOGO_DARK'),
    ],
];
```

- `url` — the route prefix **and** the React router basename the panel receives, so one change moves
  both halves at once. The route below it is `{splat?}` matching `.+`: every path under the prefix
  falls through to the panel, which is why the prefix must not overlap an existing section of the
  app
- `middleware` — replaces the whole list, it is not merged. Keep `web`: the page is Blade-served and
  the panel authenticates with the session cookie -> `access-control.md`
- `locales` — reaches the frontend as `luminix.admin.locales` (-> `boot-data.md`). It does not
  select the language; `config('app.locale')` does -> `translations.md`
- `brand` — the mark in the panel's app bar, published as `luminix.admin.brand` (-> `boot-data.md`).
  `logo` and `logo_dark` are URLs the browser fetches as-is, so point them at something the app
  serves, e.g. `/brand/logo.svg` in `public/`; the panel draws them as a 40x40 square. `logo_dark`
  shows under `prefers-color-scheme: dark` and falls back to `logo`; with neither set the Luminix
  mark stays. `name` is the image's alt text and falls back to `config('app.name')`. Setting these
  replaces overriding `Layout.AppLogo` through the `componentMap` reducer (`@luminix/mui-cms`) just to swap the mark

Config keys are read as `config('luminix.admin.*')` whether or not the file was published — the
package merges its own defaults under that key.
