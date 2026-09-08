# Frrame — Framework Reference

> Status: **this repository is the framework/blueprint itself, not an application.** This doc describes *roles and base directories*, not files, so it stays true regardless of what any given Frrame project ends up building. For how this repo currently fills those roles — specific classes, dev setup, everything else — see [`implementation.md`](implementation.md).

> Audience note: written for the coding agent of whoever **adopts** Frrame for their own project, not only for work on this repo directly.

## Why this exists

Most PHP frameworks force mandatory machinery — a front controller you can't opt out of, a router owning the URL space via `mod_rewrite`, a request/session wrapper every layer is coupled to — before a single feature gets written, often assuming hosting control a project may not have. Frrame's answer: routing works without needing URL-rewrite access at all, and everything else that looks "core" (sessions, HTTP headers, DB access, …) is an optional, independent, swappable class living in the project's own `app/`, not a vendor package. Nothing forces a component to depend on another; deleting one should never break the rest.

## Base directories

Only this much is a rule:

- `app/` — PHP application code. Composer psr-4 root.
- `resource/` — everything that isn't PHP logic: views, i18n strings, migrations, front-end assets.
- `script/` — CLI scripts (migrations, seeders), run via CLI, never through the webserver.
- `doc/` — project documentation, human-facing.
- `log/` — runtime log output. Generated, not something to commit.

Where the webserver's docroot points is deliberately *not* on this list — a `public/` folder is a common, traditional-feeling choice, not a requirement. See `implementation.md`'s Routing section for what that actually means in practice.

## app/ and resource/, briefly

Subdirectory names below are this repo's own convention, not a mandate — what should carry over to any Frrame project is the *distinction between roles*, not these exact names:

- `app/Base/` — blueprint: abstract classes concrete code extends. Yours to edit.
- `app/Controller/`, `app/Model/`, `app/Logic/`, `app/Dictionary/`, `app/Factory/` — concrete implementations of a blueprint, domain process classes, fixed value sets, and construction helpers, respectively.
- `app/Component/` — independent, single-concern classes that must not depend on each other.
- `app/Facade/` — the one layer allowed to compose more than one independent piece at once.
- `app/Util/` — self-contained helpers, no dependency in either direction.
- `app/View/` — whatever turns data into output.
- `resource/asset/`, `resource/i18n/`, `resource/migration/`, `resource/view/` — front-end source, translation strings, schema definitions, templates.

None of these are required to exist. A project that doesn't need a role doesn't need its directory.
