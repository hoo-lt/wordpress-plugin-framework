<?php

namespace Hoo\WordPressPluginFramework\Router\Routes;

use Hoo\WordPressPluginFramework\Hooker\Hooks\HookInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;

interface RouteInterface
{
	public function withMiddlewares(MiddlewareInterface ...$middlewares): RouteInterface;

	public function hook(): HookInterface;
}
