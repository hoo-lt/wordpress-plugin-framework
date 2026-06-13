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
		protected RouteFactoryInterface $routeFactory,
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
		return new static($this->routeFactory, $this->hooker, $routes);
	}

	public function withoutRoutes(): static
	{
		return new static($this->routeFactory, $this->hooker, []);
	}

	public function withRoute(RouteInterface $route): static
	{
		return $this->withRoutes(...$this->routes, $route);
	}

	public function adminAjax(string $action, Closure $closure, ?Closure $middlewaresBuilderClosure = null): static
	{
		return $this->withRoute(
			$this->routeFactory->adminAjax($action, $closure, $middlewaresBuilderClosure),
		);
	}

	public function feed(string $name, Closure $closure, ?Closure $middlewaresBuilderClosure = null): static
	{
		return $this->withRoute(
			$this->routeFactory->feed($name, $closure, $middlewaresBuilderClosure),
		);
	}

	public function rest(string $routeNamespace, string $route, Closure $closure, Method $method, ?Closure $middlewaresBuilderClosure = null): static
	{
		return $this->withRoute(
			$this->routeFactory->rest($routeNamespace, $route, $closure, $method, $middlewaresBuilderClosure),
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
