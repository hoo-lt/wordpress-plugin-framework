<?php

namespace Hoo\WordPressPluginFramework\Pipeline;

use Closure;
use Hoo\WordPressPluginFramework\Http\Request\RequestInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;

readonly class Pipeline implements PipelineInterface
{
	public function __construct(
		protected RequestInterface $request,
		protected array $middlewares = [],
	) {
	}

	public function withMiddlewares(MiddlewareInterface ...$middlewares): static
	{
		return new self(
			$this->request,
			$middlewares,
		);
	}

	public function __invoke(Closure $closure): mixed
	{
		return array_reduce(array_reverse($this->middlewares), fn(Closure $closure, MiddlewareInterface $middleware): Closure => fn(RequestInterface $request): mixed => $middleware($request, $closure), $closure)($this->request);
	}
}