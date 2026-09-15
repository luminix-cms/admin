# Configuration — `config/luminix/admin.php`

Publish it with `php artisan vendor:publish --tag=luminix-config`; the tag is shared, so every
installed `luminix/*` package writes its config in the same run.

```php
return [
    'url'        => env('LUMINIX_ADMIN_URL', '/admin'),
    'middleware' => ['web', 'auth', 'can:view-admin-panel'],
    'locales'    => ['en', 'pt-BR'],
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

Config keys are read as `config('luminix.admin.*')` whether or not the file was published — the
package merges its own defaults under that key.
