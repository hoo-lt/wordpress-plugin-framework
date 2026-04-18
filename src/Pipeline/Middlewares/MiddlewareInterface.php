<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares;

interface MiddlewareInterface
{
	public function __invoke(callable $callable): mixed;
}