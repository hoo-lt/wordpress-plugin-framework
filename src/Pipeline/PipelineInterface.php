<?php

namespace Hoo\WordPressPluginFramework\Pipeline;

use Closure;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;

interface PipelineInterface
{
	public function withMiddlewares(MiddlewareInterface ...$middlewares): static;

	public function catchException(Closure $catchExceptionClosure): static;
	public function catchThrowable(Closure $catchThrowableClosure): static;

	public function __invoke(Closure $closure): mixed;
}