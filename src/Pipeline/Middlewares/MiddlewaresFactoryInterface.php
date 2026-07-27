<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares;

use Closure;

interface MiddlewaresFactoryInterface
{
	public function create(Closure $closure): MiddlewaresInterface;
	public function tryCreate(?Closure $closure): ?MiddlewaresInterface;
}
