<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate;

use Closure;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;

interface MiddlewareFactoryInterface
{
	public function create(Closure $validatorsBuilderClosure): MiddlewareInterface;
}
