<?php

namespace Hoo\WordPressPluginFramework\Router;

use Closure;
use Hoo\WordPressPluginFramework\{
	Router\Routes\RouteInterface,
	Router\Routes\RouteFactoryInterface,
	Hooker\HookerInterface,
	Http\Method\Method,
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

	public function withAdminAjaxRoute(string $action, Closure $closure): static
	{
		return $this->withRoutes(
			$this->routeFactory->adminAjax($action, $closure),
		);
	}

	public function withFeedRoute(string $name, Closure $closure): static
	{
		return $this->withRoutes(
			$this->routeFactory->feed($name, $closure),
		);
	}

	public function withRestRoute(string $routeNamespace, string $route, Closure $closure, Method ...$methods): static
	{
		return $this->withRoutes(
			$this->routeFactory->rest($routeNamespace, $route, $closure, ...$methods),
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
