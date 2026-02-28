<?php

namespace Hoo\WordpressPluginFramework\Middleware;

interface MiddlewareInterface
{
	public function __invoke(object $object, callable $callable): mixed;
}