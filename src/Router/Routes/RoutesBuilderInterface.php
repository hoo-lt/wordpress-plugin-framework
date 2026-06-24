<?php

namespace Hoo\WordPressPluginFramework\Router\Routes;

use Closure;
use Hoo\WordPressPluginFramework\Http\Method\Method;

interface RoutesBuilderInterface
{
	public function routes(): array;
	public function withRoutes(RouteInterface ...$routes): static;
	public function withoutRoutes(): static;

	public function withRoute(RouteInterface $route): static;

	public function adminAjax(string $action, Closure $closure, ?Closure $middlewaresBuilderClosure = null): static;
	public function feed(string $name, Closure $closure, ?Closure $middlewaresBuilderClosure = null): static;
	public function rest(string $routeNamespace, string $route, Closure $closure, Method $method, ?Closure $middlewaresBuilderClosure = null): static;

	public function build(): array;
}
