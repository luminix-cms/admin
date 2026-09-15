# Getting started

```bash
composer require luminix/admin                    # pulls luminix/backend and luminix/frontend
php artisan luminix:admin-ui                      # Vite mode only -> ui-modes.md
php artisan vendor:publish --tag=luminix-config   # optional -> configuration.md
```

The provider registers one catch-all `GET` route under `luminix.admin.url` (default `/admin`) that
renders a single Blade page; React routes everything below the prefix from there. There is no
controller, view or route to write, and without the artisan command the panel already works —
served from a CDN bundle.

## The app must define the panel gate

The default middleware ends in `can:view-admin-panel` and the package defines no such gate:

```php
// AppServiceProvider::boot()
Gate::define('view-admin-panel', fn ($user) => $user->is_admin);
```

It guards panel *entry* only. Every row the panel reads or writes afterwards travels through the
REST API and its own per-model gates -> `luminix/backend`.

## What fills the drawer

The navigation is a dashboard entry plus one entry per model in the boot manifest, sorted by model
name. A model gets there by using `LuminixModel` (-> `luminix/backend`) and surviving
`luminix.frontend.models.exclude` (-> `luminix/frontend`). Nothing in this package lists models, so
an empty drawer is never fixed here.

## Done when

The `url` path renders the panel for a user the gate allows and the drawer lists your models.

Two failures look alike and are not:

- **empty drawer, panel otherwise fine** — the model never reached the manifest -> `luminix/frontend`
- **shell renders, every list errors with `401`** — the API rejects the panel's session cookie
  -> `access-control.md`
