<?php

namespace Hoo\WordPressPluginFramework\Http\Request\Routes;

use ArrayIterator;
use Traversable;

readonly class Routes implements RoutesInterface
{
	public function __construct(
		protected array $routes,
	) {
	}

	public function route(string $key): mixed
	{
		return $this->routes[$key] ?? null;
	}

	public function withRoute(string $key, mixed $route): static
	{
		$routes = $this->routes;
		$routes[$key] = $route;

		return new static($routes);
	}

	public function withoutRoute(string $key): static
	{
		$routes = $this->routes;
		unset($routes[$key]);

		return new static($routes);
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator($this->routes);
	}

	public function count(): int
	{
		return count($this->routes);
	}
}
