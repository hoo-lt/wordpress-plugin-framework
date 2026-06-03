<?php

namespace Hoo\WordPressPluginFramework\Router\Routes;

use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;

interface RouteInterface
{
	public function withMiddlewares(MiddlewareInterface ...$middlewares): static;
	public function withoutMiddlewares(): static;

	public function withMiddleware(MiddlewareInterface $middleware): static;

	public function hooks(): array;
}
