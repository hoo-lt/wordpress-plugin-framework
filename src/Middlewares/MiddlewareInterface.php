<?php

namespace Hoo\WordPressPluginFramework\Middlewares;

interface MiddlewareInterface
{
	public function __invoke(object $object, callable $callable): mixed;
}