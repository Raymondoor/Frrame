# Frrame

A way of conceptualizing and organizing an application.

Frrame is expressed as a directory structure and a set of conventions for understanding the roles of the things within it.

It is intentionally open. The structure is a starting point for an application, not a complete prescription for how an application must be implemented.

## Structure

A typical Frrame project begins with:

```text
app/        application code

resource/   application resources

script/     executable scripts

doc/        documentation

log/        runtime logs
```

A project may add, remove, or reorganize parts of this structure as its requirements develop.

The internal organization of `app/` and `resource/` is similarly open. Controllers, models, components, dictionaries, views, and other roles may be useful, but an application is not required to contain all of them.

## What Frrame is

Frrame is not a runtime or a collection of mandatory application components.

There is no Frrame application object that everything must pass through, nor is there a prescribed implementation for concerns such as routing, persistence, sessions, authentication, or rendering.

Those are decisions belonging to the application.

Frrame instead provides a starting way to organize those decisions.

## Starting a project

The repository itself can be used as a starting point for a PHP application. Remove the example application code and replace it with the code belonging to the project being built.

The current repository uses Composer, PHPUnit, Vite, and a `public/` document root. These are the choices of this repository and are not requirements of Frrame.

## Documentation

The `agent/structure/` directory contains two different kinds of information:

* `concept.md` describes Frrame's intended scope and structure.
* `implementation.md` describes the concrete implementation currently present in this repository.

Keeping those separate is intentional.

## Status

Frrame is small by design.

Its purpose is not to provide every tool an application might need, but to provide a coherent place in which an application can be built.

## Getting Started

To use this repository as a starting point:

```bash
composer create-project raymondoor/frrame my-project
cd my-project
```

Install the dependencies:

```bash
composer install
npm install
```

Copy `.env.sample` to `.env` and configure the application as needed.

The repository's own development setup uses Vite and PHPUnit. Refer to `package.json` and `phpunit.xml` for the available commands and configuration.


Run the development server:

```bash
npx vite
```