<?php

namespace Hoo\WordPressPluginFramework\Pipeline;

use Hoo\WordPressPluginFramework\Pipeline\Middlewares;

readonly class Pipeline implements PipelineInterface
{
	public function __construct(
		protected array $middlewares = [],
	) {
	}

	public function withMiddlewares(Middlewares\MiddlewareInterface ...$middlewares): PipelineInterface
	{
		return new self(
			$middlewares,
		);
	}

	public function __invoke(callable $callable): mixed
	{
		return array_reduce(array_reverse($this->middlewares), fn($callable, $middleware) => fn() => $middleware($callable), $callable)();
	}
}