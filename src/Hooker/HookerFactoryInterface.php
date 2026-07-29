<?php

namespace Hoo\WordPressPluginFramework\Hooker;

use Closure;

interface HookerFactoryInterface
{
	public function create(Closure $closure): HookerInterface;
}
