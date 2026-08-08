# Agent Instructions — Frrame PHP

Read [`framework.md`](framework.md) before making any structural change (new top-level directory, a "core"/mandatory class, a routing layer, a DI container, etc.). It explains the philosophy this repo is built on, the *role* each conventional directory plays, and why several things a normal PHP framework has are deliberately absent. It deliberately talks about directories and roles, not specific files.

For what actually currently exists in this repo — the specific classes filling those roles today — see [`implementation.md`](implementation.md). That's this project's own reference implementation, useful as precedent to stay consistent with, not a spec to enforce elsewhere.

## What this repo is right now

**Frrame is a blueprint, not an application.** There is no product logic here yet — `WelcomeController` and `home.php` are a working example, not a feature. Do not treat the current file layout as a fixed spec to preserve at all costs; do not treat it as sacred either. When asked to build a real feature, extend the pattern rather than inventing a competing one, but feel free to add/remove/reshape files under `app/` and `resource/` as the task requires — see `framework.md` for the boundary between what's fixed and what's opinion.

## Ground rules

- **Nothing outside the base directories is mandatory.** `app`, `resource`, `script` exist as agreed top-level roots; what goes *inside* them (subfolders, class names, patterns) is not dictated. Even the webroot isn't fixed to `public/` — see `framework.md`'s Routing section; this repo just happens to use one. Don't assume a "correct" place for a new class exists — pick something consistent with neighboring files, or ask.
- **The one thing Frrame guarantees: routing never requires URL-rewriting** (`mod_rewrite`, nginx `try_files`, etc.) — an entry file's path relative to the docroot remains the route on its own. That's a feature for anyone deploying to hosting they don't control, not a restriction — it's also the only thing Frrame forbids working around. A router, a DI/service container, an ORM, a global exception handler — none of those are off-limits, they're just not shipped by default because a blueprint shouldn't presume what a given project needs. See `framework.md`'s "The one guarantee" section before assuming anything beyond routing is restricted.
- **Classes here are optional utilities, not framework contracts.** `Session`, `Request`, `I18n`, etc. under `app/Component/` are conveniences the app owner chose to keep. Whether a given concern (cookies, mail, caching, whatever) gets its own wrapper class at all is a per-project call, not something the framework takes a stance on — don't add one unprompted just because a conventional framework would have it, and don't read the absence of one as an oversight to fix either.
- **Prefer static, dependency-free classes** consistent with the existing `Component`/`Facade` style, unless the task specifically calls for instances/DI.
- **Tech stack is not fixed.** dotenv, Monolog, Vite, Alpine.js, htmx are what the current owner picked for this instance — treat them as swappable, not as framework requirements, when advising or scaffolding.

## Commands

```bash
composer install              # PHP deps (vlucas/phpdotenv, monolog, raymondoor/migrr)
npm install                   # JS deps (vite, alpinejs, htmx.org)
npm run dev                   # Vite dev server (localhost:5173), used when APP_PROD=0
npm run build                 # Vite production build -> public/dist (manifest-driven)
php script/migration/up.php   # Run migrations listed in that file
```

Config comes from `.env` (copy from `.env.sample`); this project's bootstrap order is documented in `implementation.md`.

## Before you add something framework-shaped

If a task seems to call for a router, an actual middleware pipeline (chain/onion, before/after hooks), an ORM, or a DI container: `app/Facade/MiddlewareFacade.php` is **not** that — it's just a named bundle of per-entry-point bootstrap calls (see `MiddlewareFacade::web()` in `implementation.md`'s Facade tour). Check `implementation.md`'s Known Gaps section, and prefer asking the user how *they* want a real pipeline shaped over importing a pattern from a conventional framework. That question is usually the point of using Frrame at all.

Also: independence between components is a framework-level guarantee, not a project-level one. A component a mature project calls from everywhere (`Session`, `MiddlewareFacade::web()`) can be practically load-bearing even though nothing in the framework forces it — check real call sites before treating something as a safe-to-remove leaf.
