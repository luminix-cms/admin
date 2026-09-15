# Developing `luminix/admin`

`src/` is deliberately thin: one catch-all route, one Blade page, one artisan command and one
boot-data reducer. The panel itself is React and lives in `@luminix/mui-cms`.

## Two audiences, two trees

| Tree | Written for | Language | Ships |
|---|---|---|---|
| `skill/SKILL.md` + `skill/references/` | an agent **consuming** the package in an app | English | yes |
| `AGENTS.md` (this file), `CLAUDE.md` | an agent **developing** the package | English | no |
| `README.md` | a human landing on Packagist | Portuguese | yes |
| `docs/` | a human reading at tutorial length | Portuguese | no |

`.gitattributes` decides what ships. `git archive HEAD | tar -t` must list `skill/`, and never this
file, `CLAUDE.md`, `docs/`, `workbench/`, `.github/` or the test config.

An app that ran `vendor:publish --tag=luminix-skill` holds a copy of `skill/`, so a fix here
reaches it on that app's next publish with `--force`.

## Writing `skill/`

- update it when a change is observable from a consuming app: a route, a config key, a gate name, a
  boot-data key, a published file, a translation contract. Internal refactors leave it alone
- an API described there that `src/` does not have is a bug in `skill/`
- it describes the behaviour of this commit. What an older release did belongs to the release notes
- every sentence serves the reader's current task and says something the agent could not get from a
  glance at the repository
- describe the package, not the documentation system: no prose about where the guide ships from,
  how skills are found, or what else exists in the ecosystem. Name the neighbouring package when
  the answer lives outside this one

## Working here

There is no host app. `orchestra/testbench` builds a throwaway Laravel around the package, and
`workbench/` holds it — including the tests, in `workbench/app/Tests`, which is where `phpunit.xml`
points. The `tests/` mapping in `composer.json` is vestigial; nothing is there.

```bash
composer test                                  # testbench package:test
composer artisan luminix:admin-ui              # the command, inside the throwaway app
composer serve                                 # build the workbench app and serve it
```

`composer lint` is declared but PHPStan is neither a dev dependency nor configured — it does not run.

`workbench/app/Tests/TestCase.php` sets `luminix.admin.middleware` to `['web', 'auth']`, so the
`can:view-admin-panel` leg of the default middleware is never exercised by the suite, and registers
the single workbench model through `luminix.backend.models.include`.

`AdminServiceProvider::CMS_VERSION` and `::PEER_DEPENDENCIES` are the only source for both the
unpkg bundle URLs and the `package.json` that `luminix:admin-ui` writes. `UiCommandTest` asserts
that resulting `package.json` literally, so bumping either constant means editing
`$expectedPackageJson` in the same commit.

## Git

- `v1.x` is the release branch; work on `feat/`/`fix/` branches and merge into it
- every push to `v1.x` runs the 10-job matrix (PHP 8.2-8.5 x Laravel 11/12/13) and, only if it is
  green, cuts a tag + Release
- semver comes from the commit subject: `(MAJOR)` -> major, `(MINOR)` -> minor, absence -> patch
- commit messages and branch names in português
