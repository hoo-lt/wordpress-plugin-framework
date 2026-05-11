<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Catch;

use Closure;
use Hoo\WordPressPluginFramework\Http\Request\RequestInterface;
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

	public function __invoke(RequestInterface $request, Closure $closure): mixed
	{
		try {
			return ($this->middleware)($closure)($request);
		} catch (MiddlewareException $middlewareException) {
			return ($this->closure)($middlewareException);
		}
	}
}