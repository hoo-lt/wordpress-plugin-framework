<?php

namespace Hoo\WordPressPluginFramework\Router\Routes;

use Closure;
use Hoo\WordPressPluginFramework\Router\Routes\Rest\Method\Method;

interface RouteFactoryInterface
{
	public function feed(string $name, Closure $closure): RouteInterface;

	public function rest(string $namespace, string $route, Method $method, Closure $closure): RouteInterface;
}
