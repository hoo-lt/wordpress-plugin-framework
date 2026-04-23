<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Throw;

use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareException;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareTrait;

readonly class Middleware implements MiddlewareInterface
{
	use MiddlewareTrait;

	public function __invoke(Closure $closure): mixed
	{
		throw new MiddlewareException('just throwing to test pipeline', 'throw_error');
	}
}