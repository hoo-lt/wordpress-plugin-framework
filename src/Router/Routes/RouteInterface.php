<?php

namespace Hoo\WordPressPluginFramework\Router\Routes;

use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;

interface RouteInterface
{
	public function withMiddlewares(MiddlewareInterface ...$middlewares): RouteInterface;

	public function hooks(): array;
}
