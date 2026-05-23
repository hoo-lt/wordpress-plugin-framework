<?php

namespace Hoo\WordPressPluginFramework\Router;

use Hoo\WordPressPluginFramework\{
	Router\Routes\RouteInterface,
	Hooker\HookerInterface,
};

readonly class Router implements RouterInterface
{
	public function __construct(
		protected HookerInterface $hooker,
		protected array $routes = [],
	) {
	}

	public function withRoutes(RouteInterface ...$routes): static
	{
		return new self(
			$this->hooker,
			$routes
		);
	}

	public function __invoke(): void
	{
		foreach ($this->routes as $route) {
			$hooker = $this->hooker->withHooks($route->hook());
			$hooker();
		}
	}

	public function up(): void
	{
		foreach ($this->routes as $route) {
			$closure = $route->hook()->closure();
			$closure();
		}

		flush_rewrite_rules();
	}

	public function down(): void
	{
		flush_rewrite_rules();
	}
}
