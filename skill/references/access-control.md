# Panel access

Two independent layers. The middleware on the panel route decides who gets the shell; the API gates
of `luminix/backend` decide what that shell may read and write. Neither substitutes for the other:
a user the panel lets in sees only the models their `read-{alias}` gate allows, and a user with
CRUD gates but no `view-admin-panel` never reaches the panel at all.

## Entry

With the default `['web', 'auth', 'can:view-admin-panel']`:

| Request | Result |
|---|---|
| unauthenticated, browser | `302` to the app's login route |
| unauthenticated, JSON/XHR | `401` |
| authenticated, `view-admin-panel` denies | `403` |
| authenticated, allowed | the panel page, at any path under the prefix |

The ability is checked with no model argument, so the gate takes only the user:

```php
Gate::define('view-admin-panel', fn ($user) => $user->is_admin);
```

Per-model authorization cannot happen at this layer — the route is a catch-all with no model bound
to it. Hiding a model from the panel is its read gate's job -> `luminix/backend`.

Setting `middleware` replaces the list. Dropping `can:view-admin-panel` opens the panel to every
authenticated user; there is no second check behind it.

## The API must accept the panel's session

The page is served through `web` and authenticates with the session cookie. Every request the panel
makes afterwards goes to the generated API, which carries
`luminix.backend.security.middleware` — package default `['api', 'auth']`, a stack that starts no
session. Left at the default, the shell renders and every list answers `401`. Set it to
`['web', 'auth']` -> `luminix/backend`.
