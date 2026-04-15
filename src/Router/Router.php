<?php

namespace Hoo\WordPressPluginFramework\Router;

use Hoo\WordPressPluginFramework\Pipeline\PipelineInterface;
use Hoo\WordPressPluginFramework\Route\RouteInterface;
use Hoo\WordPressPluginFramework\Route\Type\Type;

class Router
{
	protected array $routes = [];

	public function __construct(
		protected readonly PipelineInterface $pipeline,
	) {
	}

	public function addRoutes(RouteInterface ...$routes): void
	{
		$this->routes = [
			...$this->routes,
			...$routes
		];
	}

	public function __invoke(): void
	{
		foreach ($this->routes as $route) {
			$handler = fn(mixed ...$args) => $this->pipeline
				->withMiddlewares(...$route->middlewares())
			(fn() => ($route->handler())(...$args));

			match ($route->type()) {
				Type::Action => add_action($route->hook(), $handler, $route->priority(), PHP_INT_MAX),
				Type::Filter => add_filter($route->hook(), $handler, $route->priority(), PHP_INT_MAX),
			};
		}
	}
}
