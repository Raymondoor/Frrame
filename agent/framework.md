# Frrame — Framework Reference

> Status: **this repository is the framework/blueprint itself, not an application.** There is no product feature here to preserve — this doc describes *roles*, not files, precisely so it stays true regardless of what any given Frrame project ends up building.

> Audience note: this file is written for the coding agent of whoever **adopts** Frrame for their own project — not only for work on this repo directly. It describes what's structurally fixed and what each conventional directory is *for*. It deliberately does not describe specific classes or files — those belong to whichever project you're in. For this repo's own current files (the creator's reference implementation, not a spec), see [`implementation.md`](implementation.md).

## Why this exists

Frrame is a reaction to how much of a typical PHP framework is *mandatory*: a front controller you can't opt out of, a request object every layer is coupled to, a router that owns your URL space, a session wrapper you must use even for a project that will only ever run on one shared host. None of that is wrong to want — but it's not always wanted, and once a project is wired to it, ripping it back out is expensive.

Frrame's answer is to keep almost nothing mandatory except a handful of top-level directories and the *roles* their conventional subdirectories play — not the specific classes filling those roles. A "core" concern like sessions or HTTP headers is just a plain, static, swappable class the app owner opted into; deleting it should never break anything else, because nothing else is allowed to depend on it.

Two concepts drive every decision here:

1. **Your blueprint, your service.** A "blueprint" directory (abstract classes concrete code extends) is yours to edit — not a vendor class you're forbidden to touch. If the blueprint doesn't fit, edit the blueprint.
2. **No hierarchy between components.** Independent, service-style classes don't depend on each other and nothing *in the framework* forces request handling to route through any particular one of them. Each is a leaf you can adopt, ignore, or delete independently. The moment components start requiring each other, one of them becomes load-bearing and stops being optional — that's the exact trap this structure avoids at the framework level.

   This is a structural guarantee, not a promise about your app. Once a project actually adopts something and calls it from everywhere, it becomes *practically* load-bearing through ordinary accumulated use, even though nothing in the framework itself required that. That's expected and fine; it's just no longer the framework's independence doing the work, it's the app's. Don't read "independent by design" as "safe to rip out at any point in a mature project" — check actual call sites, not just the absence of a framework-level dependency, before assuming something is still a leaf.

3. **Framework code lives in your `app/`, not in `vendor/`.** Unlike a typical framework where the request/session/response machinery is a black-box package you extend into but never edit, everything that plays a Frrame role lives in your own project source — visible, diffable, editable in place. Real `vendor/` dependencies are narrow libraries called *from within* your own classes, never a base package your code has to extend, and never "the framework" in the way `vendor/laravel/framework` would be. There's no hidden layer to go dig through someone else's package to understand.

## What's actually fixed

Only this much is a rule — the rest of this document is about the *roles* commonly given to their subdirectories, not a mandate that those subdirectories must exist or be named this way:

- `app/` — PHP application code. Composer psr-4 root.
- `resource/` — everything that isn't PHP logic: views, i18n strings, migrations, front-end assets (JS/CSS source).
- `script/` — one-off / CLI scripts (migrations, seeders, maintenance tasks), run via CLI, never through the webserver.
- `doc/` — project documentation, human-facing (architecture notes, product context, …). See [Directory roles](#directory-roles-naming-is-convention-the-role-is-the-point) for how this repo splits it up.
- `log/` — runtime log output. Generated content, not something to commit — see `implementation.md` for how this repo keeps the folder tracked without tracking its contents.

Where the webserver's docroot points is *not* on this list on purpose — see [Routing](#routing--works-without-url-rewriting-doesnt-forbid-it-either). A `public/` folder is the common, traditional-feeling choice for that (and this repo uses one), but it's a choice, not a requirement.

## Directory roles (naming is convention, the role is the point)

These are the roles a Frrame project typically ends up giving to subdirectories of `app/` and `resource/`. The names below are simply what this repo currently calls them — a project is free to call a "blueprint" directory something else entirely, split a role across more folders, or merge two roles into one. What should carry over to any Frrame project is the *distinction between roles*, so unrelated concerns don't end up entangled in one file just because there was nowhere else obvious to put them:

- **Blueprint** (here: `app/Base/`) — abstract classes that define a contract (e.g. "a controller has a static `index()`"). Concrete implementations extend these. Since these are your files, editing the blueprint itself when it doesn't fit is expected, not a workaround.
- **Concrete implementation of a blueprint** (here: `app/Controller/`) — classes that `extends` something in the blueprint directory. Naming/dispatch of these is not prescribed (see Routing) — a blueprint doesn't have to be a "Controller" at all.
- **Independent service-style utility** (here: `app/Component/`) — optional, static-by-convention classes wrapping a single concern (a request, a session, a DB connection, translations…). The defining rule is *not* what they wrap, it's that they must not depend on each other. Delete one and nothing else in this role should break.
- **Glue / composition layer** (here: `app/Facade/`) — code that composes two or more independent pieces (components, view data, output formatting) for a specific use — e.g. bundling which components a given entry point needs to bootstrap, or producing HTML/output that draws on more than one source. This is the one role explicitly allowed to know about more than one component at a time; components themselves are not.
- **Self-contained helper** (here: `app/Util/`) — stateless helpers with no dependency on any other app code in either direction (nothing depends on them being present, and they depend on nothing else in the app). Safe to add, safe to delete, safe to duplicate per-project.
- **View rendering** (here: `app/View/`) — whatever turns data into output (HTML, JSON, …). Not assumed to be a templating engine — plain PHP includes are a valid, minimal choice.
- **Concrete model** (here: `app/Model/`) — concrete implementations of the blueprint Model, each mapping to something persisted (typically one table). What that mapping mechanism looks like is a project decision, not a framework one — see `implementation.md` for this repo's own convention.
- **Domain/business process** (here: `app/Logic/`) — orchestration that doesn't belong to a single Model or Controller (an auth flow is the canonical example). Distinct from the independent-service role: a Logic class is allowed to coordinate more than one Component/Model to get something done.
- **Construction helper** (here: `app/Factory/`) — for when building a Model/Logic/Dictionary instance is involved enough (multiple sources, conditional shape) to be worth pulling out of the constructor. Not required for anything simple enough to just `new` directly.
- **Fixed value sets** (here: `app/Dictionary/`) — enums, constant maps, lookup tables. Naming is genuinely arbitrary here — this repo calls it `Dictionary`, a "Value" or "Enum" directory name would mean the same thing.
- **Front-end source** (here: `resource/asset/`) — JS/CSS/etc. source for whatever build tool the project chooses; not required to exist at all for a server-rendered-only project.
- **Translation strings** (here: `resource/i18n/`) — only relevant if an i18n component is in use.
- **Schema definitions** (here: `resource/migration/`) — only relevant if the project manages its DB schema in code at all.
- **Templates** (here: `resource/view/`) — whatever the View role includes/renders.
- **Human-facing docs** (here: `doc/context/{architecture,implementation,product}/`, `doc/image/`) — architecture = design decisions and their rationale (this repo uses the MADR format for these), implementation = notes on how a specific feature was actually built, product = requirements/specs/what's-being-built-and-why. Separate from `agent/`, which is AI-facing.

None of these directories are required to exist. A project that's a pure JSON API might have no `resource/view/` or `app/View/` at all; one with no database might have no `Component` covering DB access, or no `Model`/`Logic`/`Factory`/`Dictionary` at all if it's simple enough not to need the split. Absence of a role's directory means the project doesn't need that role yet, not that something is missing.

## Testing isn't one role, it's three

`test/` doesn't map to a single role the way the directories above do — this repo splits it by *where the test came from*, not by what it tests:

- **Hand-written, isolated unit tests** (here: `test/Unit/`) — the traditional kind: one class, mocked/faked collaborators, PSR-4-autoloaded and run through PHPUnit. The only one of the three actually wired into Composer's `autoload-dev`, and the only one that's a PHPUnit suite at all.
- **Agent-written checks** (here: `test/Agent/`) — whatever a coding agent produces when asked to verify something works, and specifically *not* traditional unit tests — integration-flavored checks, a disposable script that hits a real endpoint, pokes at fake data, whatever the moment calls for. No PHPUnit `TestCase`, no class requirement even.
- **Hand-written, not-necessarily-unit checks** (here: `test/User/`) — the human equivalent of `test/Agent/`: whatever a person on the project writes by hand that isn't isolated unit coverage — e.g. a script that hits the live app over real HTTP and eyeballs the response. Also not PHPUnit.

`test/Unit/` is the one PHPUnit-run suite (`phpunit.xml` only lists it); `test/Agent/` and `test/User/` are both plain, disposable scripts run directly (`php test/Agent/whatever.php`) — the split between those two is authorship, not tooling. Know *why* a piece of coverage exists (systematic isolated coverage vs. a one-off sanity check, agent- or human-written) before assuming it belongs with the others.

## Routing — works without URL-rewriting, doesn't forbid it either

Most PHP frameworks need `mod_rewrite` (or an nginx/whatever equivalent) just to boot at all — every request gets funneled through one entry point by server config, no exceptions. Frrame doesn't need that: with zero rewrite rules and zero `.htaccess`, an `index.php` file's path, relative to wherever the webserver's docroot is pointed, already works as the route. That's the whole mechanism when you reach for nothing else — not "the" routing feature Frrame is prescribing, just what happens to require no setup. Nothing about this framework requires a `public/` folder either: this repo points its docroot at `public/` and gets `public/index.php` → `/`, `public/foo/index.php` → `/foo`, but that's this project's own choice of *where* the docroot sits, not a rule — pointing it straight at the project root and dropping `index.php`/`api/index.php` there works identically. This is what makes the framework run unmodified on cheap/shared hosting where you can't touch Apache config, can't guarantee `mod_rewrite` is available, or can't even choose the docroot — copy the files up via FTP and it works, whichever layout you picked.

None of that stops you from wanting the *other* style. If you like `mod_rewrite`-driven pretty URLs, a single dispatcher parsing `$_SERVER['REQUEST_URI']`, a real router class — go build it, wire up your own rewrite rules, whatever that style needs. Frrame doesn't ship one by default, but it doesn't forbid one either; it can be both. The point isn't "files are the correct way to route," it's that nothing about *getting a working app* depends on you having rewrite access in the first place — what you layer on top from there, including `mod_rewrite` itself, is entirely your call.

The one real tradeoff of skipping a dedicated `public/`: if the docroot is the project root, then `app/`, `resource/`, `script/`, `vendor/` become web-reachable paths too, unless the webserver config denies them explicitly (a `deny all` block, docroot-level allowlisting, etc.). A `public/` folder gets this for free by construction — everything outside it is simply not under the docroot. Skipping it moves that responsibility onto whoever configures the webserver instead. Frrame doesn't gatekeep this either way; know which tradeoff you're choosing.

What an entry point does once it's reached — whether it dispatches to something in a "Controller" directory, calls a glue/composition class first, or just writes output directly — is not part of this rule. See `implementation.md` for how this repo's own entry point currently does it.

## The one guarantee

Everything so far has been about what's *not required*. That's not the same as *not allowed*, and it's worth being explicit about which one Frrame actually is. There is exactly one thing Frrame guarantees, deliberately, every time: **getting a working app never depends on URL-rewriting** (`mod_rewrite`, nginx `try_files`, or equivalent) being available. For anyone deploying to hosting they don't fully control (no rewrite config, no `.htaccess`, no say over the docroot), that's the difference between "just copy the files up" and "now go convince someone to change a server config first." See [Routing](#routing--works-without-url-rewriting-doesnt-forbid-it-either) for the mechanics — and note that this guarantee is about what Frrame itself needs, not about what you're allowed to add. Combined with a directory structure that's plain enough to read without a manual (already covered above), this guarantee is the entire portability story, and everything else in this document exists to protect it.

Nothing else is restricted — including URL-rewriting itself, for your *own* routing choices. A service locator, a DI container, an ORM, a global exception/error handler, a `mod_rewrite`-driven router — none of these are against the spirit of Frrame, and none of them are shipped by default only because a minimal blueprint shouldn't presume what a given project needs, not because they're excluded. If a task calls for one of these, build it like you would in any codebase: check `implementation.md` for how the existing pieces are shaped so a new one stays consistent, and ask the user how they want it shaped rather than importing a stock pattern wholesale — not because Frrame forbids the pattern, but because guessing an architecture the user didn't ask for is exactly the kind of imposition this framework is trying to avoid by default.

For this repo's own current answers to "so what does it actually look like right now" — specific classes, specific files, what's stubbed vs. implemented — see [`implementation.md`](implementation.md). That file is an example to stay consistent with when working in *this* repo; it is not the rule set.
