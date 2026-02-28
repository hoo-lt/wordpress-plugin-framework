<?php

namespace Hoo\WordPressPluginFramework\Middleware;

interface MiddlewareInterface
{
	public function __invoke(object $object, callable $callable): mixed;
}