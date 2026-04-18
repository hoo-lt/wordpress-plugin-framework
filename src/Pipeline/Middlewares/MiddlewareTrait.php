<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares;

use Closure;

trait MiddlewareTrait
{
	public function catch(Closure $closure): MiddlewareInterface
	{
		return new Catch\Middleware(
			$this,
			$closure,
		);
	}
}
