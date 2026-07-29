<?php

namespace Hoo\WordPressPluginFramework\Router;

use Closure;

interface RouterFactoryInterface
{
	public function create(Closure $closure): RouterInterface;
}
