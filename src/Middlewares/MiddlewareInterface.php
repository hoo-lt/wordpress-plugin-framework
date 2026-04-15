<?php

namespace Hoo\WordPressPluginFramework\Middlewares;

interface MiddlewareInterface
{
	public function __invoke(callable $callable): void;
}