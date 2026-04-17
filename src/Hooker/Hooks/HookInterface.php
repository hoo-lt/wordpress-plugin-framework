<?php

namespace Hoo\WordPressPluginFramework\Hook;

use Closure;
use Hoo\WordPressPluginFramework\Middlewares\MiddlewareInterface;

interface HookInterface
{
	public function withMiddlewares(MiddlewareInterface ...$middlewares): HookInterface;
	public function closure(): Closure;
	public function __invoke(): void;
}
