<?php

namespace Hoo\WordPressPluginFramework\Router\Routes;

use Closure;
use Hoo\WordPressPluginFramework\Http\Method\Method;

interface RouteFactoryInterface
{
	public function feed(string $path, Closure $closure): RouteInterface;

	public function rest(string $path, Closure $closure, Method ...$methods): RouteInterface;
}
