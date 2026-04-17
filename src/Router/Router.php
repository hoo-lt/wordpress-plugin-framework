<?php

namespace Hoo\WordPressPluginFramework\Router;

use Hoo\WordPressPluginFramework\Hooker\HookerInterface;
use Hoo\WordPressPluginFramework\Route\RouteInterface;

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

	public function register(): void
	{
		foreach ($this->routes as $route) {
			$closure = $route->closure();
			$closure();
		}
	}
}
