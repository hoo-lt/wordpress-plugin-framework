<?php

namespace Hoo\WordPressPluginFramework\Router;

use Closure;
use Hoo\WordPressPluginFramework\{
	Router\Routes\RouteInterface,
	Router\Routes\RouteFactoryInterface,
	Hooker\HookerInterface,
	Pipeline\Middlewares\MiddlewaresBuilder,
};

readonly class Router implements RouterInterface
{
	public function __construct(
		protected RouteFactoryInterface $routeFactory,
		protected HookerInterface $hooker,
		protected MiddlewaresBuilder $middlewaresBuilder,
		protected array $routes = [],
	) {
	}

	public function withRoutes(RouteInterface ...$routes): static
	{
		return new static(
			$this->routeFactory,
			$this->hooker,
			$this->middlewaresBuilder,
			$routes,
		);
	}

	public function withRoute(RouteInterface $route): static
	{
		return new static(
			$this->routeFactory,
			$this->hooker,
			$this->middlewaresBuilder,
			[
				...$this->routes,
				$route,
			],
		);
	}

	public function adminAjax(string $action, Closure $closure, ?Closure $middlewaresBuilderClosure = null): static
	{
		return $this->withRoute(
			$this->routeFactory->adminAjax($action, $closure)->withMiddlewares(
				...$this->middlewares($middlewaresBuilderClosure),
			),
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

	protected function middlewares(Closure $closures): array
	{
		$middlewaresBuilder = $closures($this->middlewaresBuilder);
		if (!$middlewaresBuilder instanceof MiddlewaresBuilder) {
			//throw there
		}

		return $middlewaresBuilder->build();
	}
}
