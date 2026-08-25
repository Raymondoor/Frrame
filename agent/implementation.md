# Frrame — Reference Implementation (this repo, current state)

> This is **not** the framework spec — that's [`framework.md`](framework.md), which describes roles and directories only. This file documents the actual classes and files that currently exist in *this* repository: the creator's own recommended filling-in of those roles. Treat it as precedent to stay consistent with while working in this repo, and as one worked example of how the roles in `framework.md` can be implemented — not as something every Frrame project must match. Delete, rename, or replace any of it and nothing in the framework itself breaks.

`WelcomeController` + `home.php` is the one working example route in this repo, kept so the wiring can be seen end to end. When a real app gets built on top of this copy of Frrame, most of `app/Controller/`, `resource/view/`, `resource/i18n/` here will be replaced with that project's own.

## Directory map (current files)

```
frrame/
├─ agent/
│  ├─ AGENTS.md              # short, agent-facing rules — read first
│  ├─ framework.md           # roles/rules only — read before this file
│  └─ implementation.md      # this file
├─ app/
│  ├─ Base/                  # blueprint role
│  │  ├─ Controller.php      #   abstract, static index(): void
│  │  └─ Model.php           #   abstract; $tablename + a small fluent query builder
│  ├─ Component/             # independent-service role
│  │  ├─ Http/
│  │  │  ├─ RequestMethod.php   # static GET/POST/... checks against $_SERVER
│  │  │  ├─ RequestHeader.php   # loads + reads incoming headers
│  │  │  ├─ RequestBody.php     # $_POST/$_FILES on POST; json/urlencoded via php://input on any method
│  │  │  └─ ResponseHeader.php  # header()/redirect()/http_response_code() wrapper
│  │  ├─ DBstatement.php     # static PDO wrapper (prepare/run/select/transactions)
│  │  ├─ ExceptionHandler.php   # ::setHandler() - dev error display / prod error+exception logging
│  │  ├─ Session.php         # $_SESSION wrapper designed to avoid session locking
│  │  ├─ I18n.php            # loads resource/i18n/{namespace}/{locale}.php, t()
│  │  └─ Mail.php            # empty stub — no mailer wired up yet
│  ├─ Controller/            # blueprint-implementation role
│  │  └─ WelcomeController.php  # extends Base\Controller — the one example route
│  ├─ Dictionary/            # fixed-value-set role
│  │  └─ README.md           #   nothing built yet
│  ├─ Facade/                # glue/composition role
│  │  ├─ AssetFacade.php     # Vite dev/manifest script+css tag output
│  │  ├─ PageFacade.php      # small bag of per-page metadata (title, index, alias)
│  │  ├─ LogFacade.php       # Monolog-Level-typed log record → DB or file
│  │  └─ MiddlewareFacade.php   # per-entry-point bootstrap bundles, e.g. ::web()
│  ├─ Factory/                # construction-helper role
│  │  └─ README.md           #   nothing built yet
│  ├─ Logic/                  # domain-process role
│  │  └─ README.md           #   nothing built yet
│  ├─ Model/                  # concrete-model role
│  │  └─ LogsModel.php        #   extends Base\Model, $tablename = 'logs'
│  ├─ Util/                  # self-contained-helper role
│  │  └─ Str.php             # mb_* string helpers — one example helper, nothing more
│  ├─ View/                  # view-rendering role
│  │  └─ WebView.php         # ->set()/->render() include-based view, no compiling
│  ├─ def.php                 # ROOT_PATH/APP_PATH/.../HOME_URL constants
│  ├─ env.php                  # Dotenv::createImmutable()->load()
│  └─ ini.php                  # date_default_timezone_set() etc.
├─ doc/
│  ├─ context/
│  │  ├─ architecture/        # MADR-format decision records
│  │  ├─ implementation/      # feature-level "how it was built" notes
│  │  └─ product/             # requirements / specs / "what and why"
│  └─ image/                  # images doc/ files embed
├─ log/
│  └─ .gitignore               # `*` + `!.gitignore` — folder tracked, contents never are
├─ public/
│  ├─ index.php               # web root entry point — IS the "/" route
│  ├─ api/index.php            # a second entry point — IS the "/api" route, just an example
│  └─ dist/                    # Vite build output (manifest.json + hashed assets)
├─ resource/
│  ├─ asset/                   # front-end source (script/, style/) — Vite input
│  ├─ data/
│  │  ├─ .gitignore            # same pattern as log/ — database.db never committed
│  │  └─ database.db           # local sqlite file, gitignored
│  ├─ i18n/{namespace}/{locale}.php   # returns a nested array, dot-key lookup
│  ├─ migration/                # raymondoor/migrr schema definitions
│  └─ view/                     # PHP include-based templates, rendered by WebView
├─ script/
│  ├─ migration/up.php          # runs the migrations listed inline
│  └─ seed/run.php              # fakerphp/faker-driven seeder for `logs`
├─ test/
│  ├─ Unit/                     # PHPUnit, PSR-4-autoloaded, hand-written, isolated
│  ├─ Agent/                    # agent-written checks, not PHPUnit, no class required
│  └─ User/                     # human-written checks, not PHPUnit either
├─ composer.json / composer.lock
├─ package.json / package-lock.json
├─ phpunit.xml
├─ vite.config.mjs
└─ .env / .env.sample
```

## Bootstrap order

Three files are loaded automatically as composer `files` autoload entries (see `composer.json`), always in this order, before anything else in `app/` runs:

1. **`app/env.php`** — `Dotenv::createImmutable(ROOT_PATH)->load()`, populates `$_ENV` from `.env`, then `->required(['APP_PROD', 'APP_DEBUG'])->allowedValues(['0', '1'])` — a missing or non-`0`/`1` value for either fails loudly here rather than producing a confusing failure downstream.
2. **`app/ini.php`** — sets the timezone.
3. **`app/def.php`** — defines the path/URL constants every other file relies on: `ROOT_PATH`, `APP_PATH`, `PUBLIC_PATH`, `RESOURCE_PATH`, `HOME_URL`, `IMAGE_URL`. `HOME_URL` only appends `:$_ENV['APP_PORT']` when a port is actually set (`APP_PORT=` in `.env.sample` is meant to be left blank); `IMAGE_URL` is `HOME_URL.'/asset/image'`, no encoding applied to it.

Any entry point (`public/**/index.php`, `script/**/*.php`) only needs `require_once __DIR__.'/../vendor/autoload.php'` — Composer pulls those three in automatically, then psr-4 class autoloading (`Frrame\` → `app/`) takes over. This bootstrap-via-composer-`files` mechanism is this project's own choice, not a framework requirement.

## Routing, concretely

`public/index.php`:

```php
require_once __DIR__.'/../vendor/autoload.php';
use \Frrame\Component\Http\RequestMethod;
use Frrame\Controller\WelcomeController;
use Frrame\Facade\MiddlewareFacade;
MiddlewareFacade::web();  // ExceptionHandler + RequestHeader + RequestBody + Session
if(RequestMethod::get()){
    WelcomeController::index();
}else{
    http_response_code(404);
    exit;
}
```

Nothing requires an entry point to dispatch to a `Controller`, or to call `MiddlewareFacade` first — that's this file's own choice, followed because it reads cleanly. `public/api/index.php` is a second, much smaller entry point (currently just prints a placeholder) — it exists purely to demonstrate that `public/` subfolders are independent routes with no shared registration, not because "api" is a name Frrame expects.

## Component tour

### `app/Component/Http/*`
Four independent classes, each reading straight off PHP superglobals — none of them wrap the others:
- `RequestMethod` — static booleans (`::get()`, `::post()`, …) off `$_SERVER['REQUEST_METHOD']`.
- `RequestHeader` — `::load()` populates a static array from `$_SERVER`/`getallheaders`-style parsing; `::get()`/`::is()`/`::contains()` read it.
- `RequestBody` — `::load()` fills `$form`/`$files` from `$_POST`/`$_FILES`, but only on POST — that's where PHP's own SAPI parsing (including file uploads) happens, and this class doesn't attempt to replicate it for other methods. `$json` (for `application/json`) and, on non-POST, `$form` (for urlencoded) are read straight from `php://input` instead, so those two body types work on `PUT`/`PATCH`/`DELETE` too. `::get()` reads across the merged `$raw`.
- `ResponseHeader` — thin wrapper over `header()`, `header_remove()`, `http_response_code()`, plus a `redirect()` helper that prefixes `HOME_URL`.

### `app/Component/ExceptionHandler.php`
`::setHandler()` branches on `$_ENV`: with `APP_DEBUG=1` and `APP_PROD=0` it turns on `display_errors`/`display_startup_errors` and `error_reporting(E_ALL)`; with `APP_PROD=1` it does the opposite (errors hidden) and additionally installs a real error/exception setup — `set_error_handler()` promotes warnings/notices to thrown `ErrorException`s, and `set_exception_handler()` catches anything uncaught, discards buffered output, writes a plaintext record to `log/error.log` via `error_log(..., 3, ...)`, and returns `500` (or `503` if writing the log itself failed). Nothing calls this automatically — it only runs where something explicitly calls `ExceptionHandler::setHandler()` (currently `MiddlewareFacade::web()`).

### `app/Component/Session.php`
Wraps `$_SESSION` with `start_safe()`/`close()` around every read/write specifically to avoid PHP's session-file locking blocking concurrent requests to the same visitor. Called today from `MiddlewareFacade::web()` — nothing at the framework level requires that, it's this project's own bootstrap choice (see the practical-vs-structural-independence note in `framework.md`).

### `app/Component/DBstatement.php`
A static PDO wrapper, connection built lazily from `DB_DRVR`/`DB_HOST`/`DB_NAME`/`DB_USER`/`DB_PASS`. When `DB_DRVR=sqlite` it always connects to `ROOT_PATH/resource/data/database.db` regardless of `DB_NAME`; that file is gitignored (see `resource/data/.gitignore`), so a fresh checkout won't have one until something creates it (opening a PDO sqlite connection to a non-existent file creates it empty — running a migration or the seed script is enough).

### `app/Component/I18n.php`
`I18n::load('namespace')` includes `resource/i18n/{namespace}/{APP_LOCALE}.php` (must `return` a nested associative array) and merges it in. `I18n::t('namespace.some.key', ['placeholder' => $value])` does a dot-path lookup and `{placeholder}` interpolation (delimiters configurable via `setInterpolation`). Falls back to returning the key itself if not found — no exception, no missing-translation warning. `resource/i18n/admin/{en,ja}.php` exist as empty-but-valid namespace files (mirroring `public/`'s shape) for whenever an admin area needs them.

### `app/Facade/*`
Sit one layer above `Component`/`View` — they produce output or compose data rather than reading raw request state:
- `AssetFacade` — emits `<script>` tags. In dev (`APP_PROD=0`) points at the Vite dev server (`localhost:5173`) directly; in prod, reads `public/dist/.vite/manifest.json` to resolve hashed filenames and appends any CSS Vite extracted for that entry.
- `PageFacade` — a small mutable bag (`TITLE`/`INDEX`/`ALIAS` by default, extend freely via `->set()`) a controller fills in and a view reads (`$this->page->title()`).
- `LogFacade` — a Monolog-`Level`-typed value object; `->logToDB()` inserts into the `logs` table (see migration below), `->logToFile()` is an unimplemented stub.
- `MiddlewareFacade` — **not a middleware pipeline** (no before/after hooks, no chain/onion, nothing route-aware) — just a named bundle of per-entry-point bootstrap calls. Currently one method, `::web()`, called from `public/index.php` before dispatch:
  ```php
  public static function web():void{
      ExceptionHandler::setHandler();
      RequestHeader::load();
      RequestBody::load();
      Session::load();
  }
  ```
  The pattern to follow for other entry points: if `public/api/index.php` needs a different bootstrap set (e.g. no `Session`, plus an API-key check), add a sibling static method (`::api()`) rather than branching inside `::web()` or making `::web()` cover every case. Each entry point still opts in explicitly by calling the method it wants — nothing auto-runs.

### `app/Util/Str.php`
mb_*-based `length()`/`lower()`/`upper()` helpers. One example of the self-contained-helper role — not a hint that a `Str` class specifically is expected.

### `app/View/WebView.php`
Deliberately not a templating engine — `->render('home.php')` just `include`s a plain PHP file from `resource/view/`, with `$this` inside that file being the `WebView` instance (`->set()` data readable as `$this->key` via `__get`). `->render()` can be called again from inside an included view to compose partials (see `resource/view/home.php` including `component/public-head.php`).

### `app/Base/Model.php` + `app/Model/LogsModel.php`
The concrete-model convention this repo settled on: `Base\Model` declares an uninitialized `public static string $tablename;` (no default — reading it unset throws, which is what enforces "every concrete model sets its own"), and a concrete model just assigns it:
```php
class LogsModel extends Model{
    public static string $tablename = 'logs';
}
```
Beyond that mapping, `Base\Model` also carries a small fluent query builder, all of it routed through `DBstatement` rather than reimplementing PDO access:
- `::all(bool $iamsure = false)` — every row, capped at 100 unless `$iamsure` says otherwise.
- `::column_names()` — a driver-specific introspection query (`PRAGMA table_info` for sqlite, `INFORMATION_SCHEMA`/`information_schema.columns` for mysql/pgsql).
- `::lastInserted()` — most recent row by `id DESC`.
- `->select($columns)->where($pairs, $or)->orderby($columns)->limit($n, $offset)->run()` — builds `$this->query`/`$this->params` across chained calls (`select()` honors the `$columns` it's given), `run()` executes it via `DBstatement::select()`.

This is genuinely a lightweight ORM now, not "just a name for a table."

### `app/Logic/`, `app/Factory/`, `app/Dictionary/`
All three are currently empty except for a `README.md` stating their intended role (see `framework.md`'s Directory roles section) — nothing in this project has needed them yet.

## Front-end (optional stack, currently: Vite + Alpine.js + htmx)

`resource/asset/` is Vite's source root; `vite.config.mjs` builds `resource/asset/script/{public,admin}.js` to `public/dist/` with a manifest. Each of those two entry scripts does *convention-based* per-page code splitting off the same `window.INDEX` value `AssetFacade::index()` prints (set from `PageFacade`'s `INDEX`), via `import.meta.glob('./page/public/**/*.js')` — so adding `resource/asset/script/page/public/foo.js` auto-wires it to run only when `INDEX === 'foo'`, no registration needed. `resource/asset/script/component/` (shared JS, mirrors `style/component/`) and the `page/admin/`/`style/page/admin/` pairs exist but are currently empty — nothing to share yet, no admin page yet. `alpinejs` and `htmx.org` are `package.json` deps used by convention (see `home.js`), not framework requirements — swap or drop them per project.

## i18n content

`resource/i18n/{namespace}/{locale}.php` returns a plain nested array; `WelcomeController` loads a `common` and a `public` namespace as an example of composing more than one file per request. Locale comes from `$_ENV['APP_LOCALE']` unless passed explicitly to `I18n::load()`.

## Database, migrations & seeding

Schema is defined with `raymondoor/migrr` (`resource/migration/*.php`, e.g. `CreateLogsTable`), and `script/migration/up.php` is a plain array-driven runner you invoke with `php script/migration/up.php`. **This runner currently only calls `$schema::up()` for its return value — it never executes the resulting SQL against the database** (see Known gaps). `script/seed/run.php` follows the same plain-runner shape, using `fakerphp/faker` (a `require-dev` dependency) to insert fake rows into `logs` — real inserts through `DBstatement::run()`, not fabricated output.

## Testing

Three directories under `test/`, split by *where the coverage came from* rather than by what it tests (see `framework.md`'s "Testing isn't one role, it's three"):
- `test/Unit/` — the only one of the three that's actually a PHPUnit suite. `TestCase` classes, namespaced `Frrame\Test\Unit\*`, mapped in `composer.json`'s `autoload-dev` (`"Frrame\\Test\\Unit\\": "test/Unit/"`). `StrTest.php` and `PageFacadeTest.php` are the current examples.
- `test/Agent/` — not PHPUnit, not autoloaded, no class required. `i18n-check.php` is a plain script (`php test/Agent/i18n-check.php`) that loads a real i18n file and checks the interpolated output, printing `OK`/`FAIL` and a matching exit code.
- `test/User/` — same spirit as `test/Agent/` (plain script, no PHPUnit, no class needed), written by a human instead. `home-check.php` uses `guzzlehttp/guzzle` (`require-dev`) to `GET` the real `HOME_URL` and print the status + a body snippet — an actual live request against a running instance of the app, not a mock.

`phpunit.xml` at the project root only lists the `unit` testsuite (`test/Unit`) — `test/Agent` and `test/User` are deliberately not PHPUnit suites, so neither is listed there. Run with `vendor/bin/phpunit`.

## Known gaps / rough edges

- **`script/migration/up.php` doesn't actually run migrations.** It calls `$schema::up()`, which only *returns* the CREATE TABLE SQL string — nothing executes it (there's a `// exec()` comment marking this as unfinished). The `logs` table existing in `resource/data/database.db` right now happened some other way, not through this script. Fix by feeding the returned string to `DBstatement::exec()`.
- `app/Component/Mail.php` is an empty stub — present as a named placeholder, not implemented.
- `LogFacade::logToFile()` is a no-op stub (`return true;`) — only `logToDB()` actually does anything.
- `public/dist/` (Vite build output) is currently untracked rather than gitignored — decide deliberately whether build artifacts should be committed for this project before assuming either way.
