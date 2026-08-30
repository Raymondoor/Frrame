# Frrame

A PHP application blueprint, not a framework you learn — a small directory convention you copy and then treat as your own code.

## Philosophy

- **Nothing is mandatory except a handful of top-level directories.** Sessions, HTTP headers, DB access, i18n — all optional, independent, swappable classes that live in your own `app/`, not hidden in a `vendor/` package.
- **Routing never depends on URL-rewriting.** An entry file's path relative to wherever your webserver's docroot points *is* the route — no `mod_rewrite`, no `.htaccess`, no router required. If you don't control the webserver, this is what lets the project run unmodified anyway: copy the files up and it works.
- **Everything else is free.** A router, DI container, ORM, global exception handler — none of it is off-limits, it's just not shipped by default.

## Getting started

```bash
composer create-project raymondoor/frrame <your-app-name>
cd <your-app-name>
npm install
cp .env.sample .env   # then fill in your own values
```

Point your webserver's docroot at `public/` (or somewhere else entirely — routing doesn't care). During development:

```bash
npx vite               # Vite dev server
vendor/bin/phpunit     # test/Unit/
```

For production, `npx vite build` outputs to `public/dist/`.

## Directory structure

```
app/       PHP application code (blueprints, controllers, models, ...)
public/    webroot — every index.php here is a route
resource/  views, i18n, front-end source, DB migrations
script/    CLI scripts (migrations, seeders)
doc/       human-facing project docs
log/       runtime log output (gitignored)
test/      Unit (PHPUnit), Agent/User (disposable checks)
```

## Learn more

See [`doc/context/`](doc/context/) for architecture decisions, implementation notes, and product context.
