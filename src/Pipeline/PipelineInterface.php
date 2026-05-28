<?php

namespace Hoo\WordPressPluginFramework\Pipeline;

use Closure;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;

interface PipelineInterface
{
	public function withMiddlewares(MiddlewareInterface ...$middlewares): static;

	public function catch(Closure $closure): static;

	public function __invoke(Closure $closure): mixed;
}