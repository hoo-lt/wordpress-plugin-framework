<?php

namespace Hoo\WordPressPluginFramework\Pipeline;

use Closure;
use Hoo\WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Pipeline\Middlewares\MiddlewareInterface,
};
use Throwable;

readonly class Pipeline implements PipelineInterface
{
	public function __construct(
		protected RequestInterface $request,
		protected array $middlewares = [],
		protected ?Closure $catchClosure = null,
	) {
	}

	public function withMiddlewares(MiddlewareInterface ...$middlewares): static
	{
		return new static(
			$this->request,
			$middlewares,
			$this->catchClosure,
		);
	}

	public function catch(Closure $closure): static
	{
		return new static(
			$this->request,
			$this->middlewares,
			$closure,
		);
	}

	public function __invoke(Closure $closure): mixed
	{
		return array_reduce(
			array_reverse($this->middlewares),
			function (Closure $closure, MiddlewareInterface $middleware) {
				return function (RequestInterface $request) use ($middleware, $closure) {
					try {
						return $middleware($request, $closure);
					} catch (Throwable $throwable) {
						if ($this->catchClosure === null) {
							throw $throwable;
						}

						return ($this->catchClosure)($request, $throwable);
					}
				};
			},
			function (RequestInterface $request) use ($closure) {
				try {
					return $closure($request);
				} catch (Throwable $throwable) {
					if ($this->catchClosure === null) {
						throw $throwable;
					}

					return ($this->catchClosure)($request, $throwable);
				}
			},
		)($this->request);
	}
}