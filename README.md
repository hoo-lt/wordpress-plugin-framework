# Hoo — a modern framework for serious WordPress plugins

> Bring DI, controllers, routing and middleware to WordPress — without forcing
> the rest of WordPress to change, and without shipping Bedrock/Acorn to your users.

**Status: pre-alpha.** The API is still shifting. It is, however, already running a
real WooCommerce integration in production.

---

## The problem

Once a plugin grows past a few features, the "WordPress way" starts to hurt:

- `add_action` / `add_filter` scattered across dozens of files — *hook spaghetti*
  that's terrifying to change.
- Passing database services and API clients five levels deep into hook callbacks.
- Business logic welded to `WP_Post`, `wpdb`, nonces and `current_user_can`, so
  nothing can be unit-tested without booting all of WordPress.
- Modern stacks like Roots/Sage solve this — but only when *you* own the whole
  site. You can't ask thousands of users on shared hosting to install Acorn just
  to run your plugin.

The usual answers ("just use a container", "just use Symfony") tend to fight WordPress
core. Hoo takes the opposite stance: **let WordPress stay exactly what it is at the
boundary, and keep your domain clean behind it.**

## The idea: WordPress as the adapter, your code as the app

WordPress owns the lifecycle, routing, capabilities and persistence. Your plugin
treats every hook as an *event entering a controlled boundary* — a "moat" — and only
clean, dependency-injected code lives inside it.

Concretely:

- **Declarative registration in one place.** All your hooks and routes live in a
  single file, not scattered across class constructors.
- **Lazy, container-resolved callbacks.** Helpers return a plain `Closure`. From
  WordPress's point of view nothing changed — it still calls a normal callable.
  The controller (and its constructor dependencies) is only resolved *if and when*
  the hook actually fires. **Zero backward-compatibility cost.**
- **Middleware pipelines at the point of registration.** Auth, nonce checks,
  validation, DB transactions and timing attach *to the hook*, not inside your
  method — so your controller stays focused on one thing.
- **Self-contained.** The whole framework rides inside one ordinary plugin. The end
  user clicks "Install" on vanilla WordPress, unaware there's an OOP framework under
  the hood.

## What it looks like

```php
return hook()
    ->action('init', action(RegisterTaxonomies::class))
    ->action(
        'woocommerce_update_product',
        controller(ProductController::class, 'syncExternal'),
        10,
        fn (MiddlewaresBuilder $mw) => $mw
            ->transaction()        // wrap the hook in a DB transaction
            ->logExecutionTime()   // monitor performance
            ->validate(fn ($v) => $v
                ->body('post_title', fn ($r) => $r->string()->nullable())
            ),
    );
```

By the time execution reaches `ProductController::syncExternal()`, the request has
already been validated and wrapped in a transaction — and the controller has its
dependencies injected. The domain code never sees a nonce or a global.

## The one rule: never contest WordPress's control flow

There is a graveyard of ambitious WordPress frameworks. They didn't die because
their code was bad — they died because they tried to **own the request lifecycle**:
their own bootstrap, their own router, their own dispatch, with WordPress demoted to
"the thing that loads my container." Blade and Twig were just the visible flag of
that mindset. The moment your control flow competes with WordPress's for ownership of
the same request, you need glue — and that glue tears every time core moves the hook
order, the REST pipeline, or the bootstrap sequence underneath you.

Hoo's single non-negotiable rule: **never contest WordPress's control flow.**
WordPress keeps the lifecycle, routing, capabilities, escaping and persistence. Hoo
only adds structure *in the seams WordPress already yields* — a pipeline inside a hook
callback, a controller resolved lazily when the hook fires, middleware attached at
registration. Everything is **additive**, never a replacement. There is no glue,
because there are not two worldviews fighting to own the request.

The corollary tells you what is safe to build yourself. Shadowing WordPress's
lifecycle is fatal because it *moves* every release. Shadowing a frozen spec is not:
the internal HTTP value objects (headers, body, URL, method) mirror HTTP semantics,
which don't change — so the cost was paid once at authoring time and never rots. The
test isn't "is it custom code?" It's "does it contest something that moves?"

## Why not just Roots, or raw PHP-DI?

- **vs. the "WordPress way":** you get SOLID/DDD *where it pays off* (sync jobs,
  billing, integrations) without rewriting how WordPress fires hooks.
- **vs. Roots/Bedrock/Sage:** no full-stack commitment. Hoo is "boring" and
  decoupled — drop it into a normal, distributable plugin and rip it out later
  without touching the site.
- **vs. wiring a container by hand:** the DI container ([PHP-DI]), middleware layer
  and namespace setup are scaffolded for you, so the "upfront fight" is close to zero.

## Getting started

The framework is consumed through the [plugin scaffold]. A single
`composer create-project` gives you a working plugin with the container, hooks,
routes and middleware already wired:

```bash
composer create-project hoo-lt/wordpress-plugin my-plugin
```

The scaffold rewrites the namespace and identifiers for your new plugin on first run.

## Roadmap

The framework is being driven to a stable release through a series of tagged
milestones. Each milestone has a clear exit criterion and a git tag.

### `alpha.1` — it works (current focus)

Make the framework and the scaffold boot cleanly on a vanilla WordPress install,
end to end, with no fatal errors.

- [ ] Add the missing plugin entry file (`wordpress-plugin.php` with the plugin
      header, booting `Application`) so WordPress can activate it.
- [ ] Fix the scaffold `routes.php` — `rest()` requires an HTTP `Method`; remove the
      duplicate/placeholder routes.
- [ ] Ship the example handlers referenced by `hooks.php` / `routes.php`
      (`RegisterFeed`, `ContentController`) instead of referencing classes that
      don't exist.
- [ ] Ensure scaffold support directories exist (`views/`, `migrations/`).
- [ ] Audit the framework `src/` for the same class of errors (wrong signatures,
      dangling references); get a clean boot + a lint/static-analysis pass.

**Exit:** `composer create-project` produces a plugin that activates and runs. **Tag `alpha.1`.**

### `alpha.2` — a usable platform

Fill the gaps that stop the framework from handling real plugin work.

- [ ] **Cron** — first-class scheduled-task registration (alongside `hook()` /
      `route()`), wired through the same controller + middleware pipeline.
- [ ] **Complete the database layer** — today it is select-only. Add full CRUD
      (insert/update/delete), a query abstraction, and a path for plain SQL, so the
      DB layer is a complete way to work with custom tables.
- [ ] **Current user** — a `User` object representing the current WordPress user,
      injectable into controllers/services (so domain code reads identity through a
      typed object instead of `wp_get_current_user()` / globals).

**Exit:** a non-trivial plugin (scheduled job + custom-table CRUD + auth) can be
built without dropping to raw WordPress APIs. **Tag `alpha.2`.**

### `alpha.3` — self-contained container

Replace PHP-DI with a small, purpose-built autowiring container (the scaffold only
uses `get`, autowiring, scalar constructor overrides, and closure/array factories).

- [ ] Implement the container (`get` + shared caching, constructor autowiring,
      `constructorParameter` overrides, closure & array-callable factories, cycle
      detection) and the `autowire()` / `factory()` helpers.
- [ ] Swap the scaffold over; drop the `php-di/php-di` dependency.

**Why it matters:** removing PHP-DI also removes its transitive deps and the
compiled-container problem — shrinking the surface that has to be isolated for safe
public distribution down to essentially just the framework's own namespace.
**Tag `alpha.3`.**

### Beyond alpha (path to beta)

- **Dependency isolation for public distribution** — per-plugin prefixing (e.g.
  Strauss) so two Hoo-based plugins can never clash on a shared install.
- **Engine + add-ons** — optionally ship the framework as a foundational plugin
  with child plugins that gracefully self-disable (admin notice, no WSOD) when it's
  absent.
- **Tests & documentation** — a real test suite and expanded docs.

[PHP-DI]: https://php-di.org/
[plugin scaffold]: https://github.com/hoo-lt/wordpress-plugin
