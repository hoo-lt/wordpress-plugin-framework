<?php

namespace Hoo\WordPressPluginFramework\Hooker\Hooks;

use Closure;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;

interface HookInterface
{
	public function middlewares(): array;
	public function withMiddlewares(MiddlewareInterface ...$middlewares): static;
	public function withoutMiddlewares(): static;

	public function withMiddleware(MiddlewareInterface $middleware): static;

	public function closure(): Closure;

	public function __invoke(): void;
}
