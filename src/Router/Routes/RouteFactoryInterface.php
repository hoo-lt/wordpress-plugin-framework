<?php

namespace Hoo\WordPressPluginFramework\Route;

use Closure;
use Hoo\WordPressPluginFramework\Route\Rest\Method\Method;

interface RouteFactoryInterface
{
	public function feed(string $name, Closure $closure): RouteInterface;

	public function rest(string $namespace, string $route, Method $method, Closure $closure): RouteInterface;
}
