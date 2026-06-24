<?php

namespace Hoo\WordPressPluginFramework\Router;

use Hoo\WordPressPluginFramework\{
	Hooker\HookerInterface,
	Router\Routes\RouteInterface,
};

readonly class Router implements RouterInterface
{
	public function __construct(
		protected HookerInterface $hooker,
		protected array $routes = [],
	) {
	}

	public function routes(): array
	{
		return $this->routes;
	}

	public function withRoutes(RouteInterface ...$routes): static
	{
		return new static($this->hooker, $routes);
	}

	public function withoutRoutes(): static
	{
		return new static($this->hooker, []);
	}

	public function withRoute(RouteInterface $route): static
	{
		return $this->withRoutes(...$this->routes, $route);
	}

	public function __invoke(): void
	{
		foreach ($this->routes as $route) {
			$hooks = $route->hooksBuilder()->build();

			$hooker = $this->hooker->withHooks(...$hooks);
			$hooker();
		}
	}

	public function up(): void
	{
		foreach ($this->routes as $route) {
			$hooks = $route->hooksBuilder()->build();
			foreach ($hooks as $hook) {
				$closure = $hook->closure();
				$closure();
			}
		}

		flush_rewrite_rules();
	}

	public function down(): void
	{
		flush_rewrite_rules();
	}
}
