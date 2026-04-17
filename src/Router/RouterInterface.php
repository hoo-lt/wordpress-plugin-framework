<?php

namespace Hoo\WordPressPluginFramework\Router;

use Hoo\WordPressPluginFramework\Route\RouteInterface;

interface RouterInterface
{
	public function withRoutes(RouteInterface ...$routes): static;

	public function __invoke(): void;

	public function register(): void;
}
