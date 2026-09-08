# Agent Instructions — Frrame PHP

Read [`structure/framework.md`](structure/framework.md) before making any structural change (new top-level directory, a "core"/mandatory class, a routing layer, a DI container, etc.). It explains the philosophy this repo is built on, the base directories, and the *role* app/ and resource/ subdirectories play. It deliberately talks about directories and roles, not specific files.

For what actually currently exists in this repo — the specific classes filling those roles, dev setup, everything else — see [`structure/implementation.md`](structure/implementation.md). That's this project's own reference implementation, useful as precedent to stay consistent with, not a spec to enforce elsewhere.

## What this repo is right now

**Frrame is a blueprint, not an application.** There is no product logic here yet — `WelcomeController` and its view are a working example, not a feature. Do not treat the current file layout as a fixed spec to preserve at all costs; do not treat it as sacred either. When asked to build a real feature, extend the pattern rather than inventing a competing one, but feel free to add/remove/reshape files under `app/` and `resource/` as the task requires — see `structure/framework.md` for the boundary between what's fixed and what's opinion.

## Ground rules

- **Nothing outside the base directories is mandatory.** `app`, `resource`, `script`, `doc`, `log` exist as agreed top-level roots; what goes *inside* them (subfolders, class names, patterns) is not dictated. Even the webroot isn't fixed to `public/` — see `structure/implementation.md`'s Routing section; this repo just happens to use one. Don't assume a "correct" place for a new class exists — pick something consistent with neighboring files, or ask.
- **The one thing Frrame guarantees: getting a working app never requires URL-rewriting** (`mod_rewrite`, nginx `try_files`, etc.) to be set up — an entry file's path relative to the docroot works as the route with zero config. That's about what Frrame itself needs, not a ban on `mod_rewrite` — if a task wants traditional rewrite-driven routing, that's entirely buildable on top, same as a router, DI container, ORM, or global exception handler: none of those are off-limits, they're just not shipped by default because a blueprint shouldn't presume what a given project needs.
- **Classes here are optional utilities, not framework contracts.** `Session`, `I18n`, etc. under `app/Component/` are conveniences the app owner chose to keep. Whether a given concern (cookies, mail, caching, whatever) gets its own wrapper class at all is a per-project call, not something the framework takes a stance on — don't add one unprompted just because a conventional framework would have it, and don't read the absence of one as an oversight to fix either.
- **Prefer static, dependency-free classes** consistent with the existing `Component`/`Facade` style, unless the task specifically calls for instances/DI.
- **Tech stack is not fixed.** dotenv, Monolog, Vite, Alpine.js, htmx are what the current owner picked for this instance — treat them as swappable, not as framework requirements, when advising or scaffolding.

## Before you add something framework-shaped

If a task seems to call for a router, an actual middleware pipeline (chain/onion, before/after hooks), an ORM, or a DI container: `app/Facade/MiddlewareFacade.php` is **not** that — it's just a named bundle of per-entry-point bootstrap calls (see `MiddlewareFacade::web()` in `structure/implementation.md`). Check `structure/implementation.md`'s Known Gaps section before assuming something is broken by design rather than just unfinished, and prefer asking the user how *they* want a real pipeline shaped over importing a pattern from a conventional framework. That question is usually the point of using Frrame at all.

Also: independence between components is a framework-level guarantee, not a project-level one. A component a mature project calls from everywhere (`Session`, `MiddlewareFacade::web()`) can be practically load-bearing even though nothing in the framework forces it — check real call sites before treating something as a safe-to-remove leaf.
