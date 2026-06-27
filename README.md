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

- **PHP-Scoper build step** — prefix bundled dependencies (PHP-DI, PSR) so two
  Hoo-based plugins can never clash on a shared install.
- **Engine + add-ons** — ship the framework as a foundational plugin with child
  plugins that gracefully self-disable (admin notice, no WSOD) when it's absent.
- Test suite and expanded documentation.

[PHP-DI]: https://php-di.org/
[plugin scaffold]: https://github.com/hoo-lt/wordpress-plugin
```
