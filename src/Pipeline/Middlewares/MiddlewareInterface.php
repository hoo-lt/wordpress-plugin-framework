<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares;

use Closure;
use Hoo\WordPressPluginFramework\Http\Server\Request\RequestInterface;

interface MiddlewareInterface
{
	public function __invoke(RequestInterface $request, Closure $closure): mixed;
}