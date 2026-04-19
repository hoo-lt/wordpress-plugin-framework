<?php

namespace Hoo\WordPressPluginFramework\Router\Routes;

use Closure;
use Hoo\WordPressPluginFramework\Http\Method\Method;

interface RouteFactoryInterface
{
	public function feed(string $name, Closure $closure): RouteInterface;

	public function rest(string $route, Method $method, Closure $closure): RouteInterface;
}
