<?php

namespace Hoo\WordPressPluginFramework\Pipeline;

use Closure;
use Hoo\WordPressPluginFramework\Http\Request\RequestInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;

interface PipelineInterface
{
	public function withRequest(RequestInterface $request): static;
	public function withMiddlewares(MiddlewareInterface ...$middlewares): static;
	public function __invoke(Closure $closure): mixed;
}