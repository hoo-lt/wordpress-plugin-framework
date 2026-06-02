<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate;

use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;

interface MiddlewareFactoryInterface
{
	public function create(): MiddlewareInterface;
}