<?php

namespace Hoo\WordPressPluginFramework\Router\Routes;

use Closure;
use Hoo\WordPressPluginFramework\Http\Method\Method;

interface RouteFactoryInterface
{
	public function adminAjax(string $action, Closure $closure, ?Closure $middlewaresBuilderClosure = null): RouteInterface;
	public function feed(string $name, Closure $closure, ?Closure $middlewaresBuilderClosure = null): RouteInterface;
	public function rest(string $routeNamespace, string $route, Closure $closure, Method $method, ?Closure $middlewaresBuilderClosure = null): RouteInterface;
}
