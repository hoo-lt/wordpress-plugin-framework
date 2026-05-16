<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Catch;

use Closure;
use Hoo\WordPressPluginFramework\{
	Http,
	Pipeline,
};

readonly class Middleware implements Pipeline\Middlewares\MiddlewareInterface
{
	use Pipeline\Middlewares\MiddlewareTrait;

	public function __construct(
		protected Pipeline\Middlewares\MiddlewareInterface $middleware,
		protected Closure $closure,
	) {
	}

	public function __invoke(Http\Request\RequestInterface $request, Closure $closure): mixed
	{
		try {
			return ($this->middleware)($request, $closure);
		} catch (Pipeline\Middlewares\MiddlewareException $middlewareException) {
			return ($this->closure)($middlewareException);
		}
	}
}