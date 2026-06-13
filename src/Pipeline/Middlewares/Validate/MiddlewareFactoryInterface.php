<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate;

interface MiddlewareFactoryInterface
{
	public function create(): MiddlewareInterface;
}