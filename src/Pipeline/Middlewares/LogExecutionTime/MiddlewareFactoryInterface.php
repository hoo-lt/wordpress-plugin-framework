<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\LogExecutionTime;

use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;

interface MiddlewareFactoryInterface
{
	public function create(): MiddlewareInterface;
}