<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares;

use Closure;

interface MiddlewareInterface
{
	public function __invoke(callable $callable): mixed;
	public function catch(Closure $closure): MiddlewareInterface;
}