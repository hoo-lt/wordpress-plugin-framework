<?php

namespace Hoo\WordPressPluginFramework\Route;

use Hoo\WordPressPluginFramework\Hook\HookInterface;
use Hoo\WordPressPluginFramework\Middlewares\MiddlewareInterface;

interface RouteInterface
{
	public function withMiddlewares(MiddlewareInterface ...$middlewares): RouteInterface;

	public function hook(): HookInterface;
}
