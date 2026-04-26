<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares;

use Closure;
use Hoo\WordPressPluginFramework\Http\Request\RequestInterface;

interface MiddlewareInterface
{
	public function __invoke(?RequestInterface $request, Closure $closure): mixed;
	public function catch(Closure $closure): MiddlewareInterface;
}