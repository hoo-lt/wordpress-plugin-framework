<?php

namespace Hoo\WordPressPluginFramework\Route;

use Closure;
use Hoo\WordPressPluginFramework\Middlewares\MiddlewareInterface;
use Hoo\WordPressPluginFramework\Route\Type\Type;

interface RouteInterface
{
	public function type(): Type;

	public function hook(): string;

	public function handler(): Closure;

	public function priority(): int;

	public function middlewares(): array;

	public function withMiddlewares(MiddlewareInterface ...$middlewares): self;
}
