<?php

namespace Hoo\WordPressPluginFramework\Hook;

use Hoo\WordPressPluginFramework\Middlewares\MiddlewareInterface;

interface HookInterface
{
	public function withMiddlewares(MiddlewareInterface ...$middlewares): HookInterface;
	public function __invoke(): void;
}
