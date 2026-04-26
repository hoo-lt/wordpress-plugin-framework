<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Throw;

use Closure;
use Hoo\WordPressPluginFramework\Http\Request\RequestInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareException;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareTrait;

readonly class Middleware implements MiddlewareInterface
{
	use MiddlewareTrait;

	public function __invoke(?RequestInterface $request, Closure $closure): mixed
	{
		throw new MiddlewareException('just throwing to test pipeline', 'throw_error');
	}
}