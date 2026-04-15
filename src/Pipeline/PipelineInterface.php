<?php

namespace Hoo\WordPressPluginFramework\Pipeline;

use Hoo\WordPressPluginFramework\Middlewares;

interface PipelineInterface
{
	public function withMiddlewares(Middlewares\MiddlewareInterface ...$middlewares): PipelineInterface;
	public function __invoke(callable $callable): void;
}