<?php

namespace Hoo\WordPressPluginFramework\Routes;

use Closure;

interface RoutesFactoryInterface
{
	public function create(Closure $closure): RoutesInterface;
}
