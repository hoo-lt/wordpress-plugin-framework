<?php

namespace Hoo\WordPressPluginFramework\Hooker\Hooks;

use Closure;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;

interface HookInterface
{
	public function withMiddlewares(MiddlewareInterface ...$middlewares): HookInterface;
	public function closure(): Closure;
	public function __invoke(): void;
}
