<?php

namespace Hoo\WordPressPluginFramework\Pipeline;

use Hoo\WordPressPluginFramework\Middlewares;

interface PipelineInterface
{
	public function middlewares(Middlewares\MiddlewareInterface ...$middlewares): self;
	public function __invoke(callable $callable): mixed;
}