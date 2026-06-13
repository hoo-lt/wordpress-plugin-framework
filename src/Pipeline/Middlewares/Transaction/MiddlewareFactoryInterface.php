<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Transaction;

use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;

interface MiddlewareFactoryInterface
{
	public function create(): MiddlewareInterface;
}