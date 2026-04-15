<?php

namespace Hoo\WordPressPluginFramework\Router;

use Closure;

interface RouterInterface
{
	public function action(string $hook, Closure $handler, int $priority = PHP_INT_MAX, int $acceptedArgs = 1): Route;

	public function filter(string $hook, Closure $handler, int $priority = PHP_INT_MAX, int $acceptedArgs = 1): Route;

	public function register(): void;
}
