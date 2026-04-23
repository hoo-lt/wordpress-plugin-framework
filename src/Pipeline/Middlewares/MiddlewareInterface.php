<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares;

use Closure;

interface MiddlewareInterface
{
	public function __invoke(Closure $closure): mixed;
	public function catch(Closure $closure): MiddlewareInterface;
}