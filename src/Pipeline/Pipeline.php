<?php

namespace Hoo\WordPressPluginFramework\Pipeline;

use Closure;
use Hoo\WordPressPluginFramework\{
	Http\Server\Request\RequestInterface,
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

	public function withRequest(RequestInterface $request): static
	{
		return new static(
			$request,
			$this->middlewares,
			$this->catchClosure,
		);
	}

	public function withMiddlewares(MiddlewareInterface ...$middlewares): static
	{
		return new static($this->request, $middlewares, $this->catchClosure);
	}

	public function withoutMiddlewares(): static
	{
		return new static($this->request, [], $this->catchClosure);
	}

	public function withMiddleware(MiddlewareInterface $middleware): static
	{
		return $this->withMiddlewares(...$this->middlewares, $middleware);
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
			fn(Closure $closure, MiddlewareInterface $middleware) => fn(RequestInterface $request) => $this->tryCatch($request, $closure),
			fn(RequestInterface $request) => $this->tryCatch($request, $closure),
		)($this->request);
	}

	protected function tryCatch(RequestInterface $request, Closure $closure): mixed
	{
		try {
			return $closure($request);
		} catch (Throwable $throwable) {
			if ($this->catchClosure === null) {
				throw $throwable;
			}

			return ($this->catchClosure)($request, $throwable);
		}
	}
}