# CDN mode and Vite mode

One file decides which mode an app is in: `resources/views/vendor/admin/cms.blade.php`. Absent, the
package's own view loads pre-built bundles from unpkg. Present, it overrides that view and the page
loads your Vite build instead. Deleting it reverts the app to the CDN.

| | CDN (default) | Vite |
|---|---|---|
| JS build | none | yours (`npm run dev` / `npm run build`) |
| Panel version | `AdminServiceProvider::CMS_VERSION`, moved only by upgrading this composer package | whatever your `package.json` resolves |
| Frontend customization | impossible | JS `AppServiceProvider` -> `@luminix/mui-cms` |

## `php artisan luminix:admin-ui`

Switches an app to Vite mode. Exactly three effects, all local to the app:

1. rewrites `package.json`: merges the pinned dependency set into `dependencies`, drops those same
   names from `devDependencies`, sorts the list. It prints the set and asks for confirmation first;
   declining skips only this step
2. publishes the `luminix-ui` tag — `resources/views/vendor/admin/cms.blade.php` (the `@vite` page),
   `resources/js/luminix-admin.jsx` (the React entry) and
   `resources/js/Providers/AppServiceProvider.js` (an empty provider, your customization hook)
3. prints the remaining manual steps

It does **not** run `npm`, and it does **not** touch `vite.config.js`. `--force` overwrites existing
published files and skips both prompt and closing instructions.

## The two manual steps

```js
// vite.config.js
laravel({ input: ['resources/js/luminix-admin.jsx', /* ... */], refresh: true })
```

```bash
npm install && npm run dev
```

## Installing the JS by hand

For a non-Laravel build pipeline, the dependency set is `@luminix/mui-cms` plus the package's
`AdminServiceProvider::PEER_DEPENDENCIES` — read the constant for the exact versions, they are
pinned there and some are exact rather than caret ranges:

```bash
npm install @luminix/mui-cms @luminix/core @luminix/react @luminix/support \
    react react-dom react-router-dom \
    @mui/material @mui/icons-material @emotion/react @emotion/styled \
    @fontsource/roboto i18next react-i18next
```

## Writing your own page

The published Blade page can be edited freely as long as it keeps `@luminixEmbed()` and a
`<div id="root">`: the directive writes the boot JSON the panel reads at startup
(-> `boot-data.md`), and the entry script mounts on `#root`.

## Upgrading

- CDN mode: `composer update luminix/admin` — `CMS_VERSION` moves the unpkg URLs with it
- Vite mode: re-run `php artisan luminix:admin-ui --force` to pick up the new pinned versions, then
  `npm install`. `--force` overwrites the published `AppServiceProvider.js`, so keep your
  customization in files it does not publish
