<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Catch;

use Closure;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareException;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareTrait;

readonly class Middleware implements MiddlewareInterface
{
	use MiddlewareTrait;

	public function __construct(
		protected MiddlewareInterface $middleware,
		protected Closure $closure,
	) {
	}

	public function __invoke(callable $callable): mixed
	{
		try {
			return ($this->middleware)($callable);
		} catch (MiddlewareException $middlewareException) {
			return ($this->closure)($middlewareException);
		}
	}
}