<?php

namespace Hoo\WordPressPluginFramework\Router;

use Hoo\WordPressPluginFramework\{
	Router\Routes\RouteInterface,
	Router\Routes\RouteFactoryInterface,
	Hooker\HookerInterface,
};

readonly class Router implements RouterInterface
{
	public function __construct(
		protected HookerInterface $hooker,
		protected RouteFactoryInterface $routeFactory,
		protected array $routes = [],
	) {
	}

	public function withRoutes(RouteInterface ...$routes): static
	{
		return new static(
			$this->hooker,
			$this->routeFactory,
			$routes,
		);
	}

	public function __invoke(): void
	{
		foreach ($this->routes as $route) {
			$hooks = $route->hooks();

			$hooker = $this->hooker->withHooks(...$hooks);
			$hooker();
		}
	}

	public function up(): void
	{
		foreach ($this->routes as $route) {
			$hooks = $route->hooks();
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
