<?php

namespace Hoo\WordPressPluginFramework\Pipeline;

use Closure;
use Hoo\WordPressPluginFramework\{
	Http\Server\Request\RequestInterface,
	Pipeline\Middlewares\MiddlewareInterface,
	Pipeline\Middlewares\MiddlewaresInterface,
};
use Throwable;

readonly class Pipeline implements PipelineInterface
{
	public function __construct(
		protected RequestInterface $request,
		protected ?MiddlewaresInterface $middlewares = null,
		protected ?Closure $closure = null,
	) {
	}

	public function request(): RequestInterface
	{
		return $this->request;
	}

	public function withRequest(RequestInterface $request): static
	{
		return new static($request, $this->middlewares, $this->closure);
	}

	public function middlewares(): ?MiddlewaresInterface
	{
		return $this->middlewares;
	}

	public function withMiddlewares(?MiddlewaresInterface $middlewares): static
	{
		return new static($this->request, $middlewares, $this->closure);
	}

	public function withoutMiddlewares(): static
	{
		return new static($this->request, null, $this->closure);
	}

	public function catch(Closure $closure): static
	{
		return new static($this->request, $this->middlewares, $closure);
	}

	public function __invoke(Closure $closure): mixed
	{
		$closure = $this->closure($closure);

		if ($this->middlewares === null) {
			return $closure($this->request);
		}

		return array_reduce(
			array_reverse(
				iterator_to_array($this->middlewares),
			),
			$this->middleware(...),
			$closure,
		)($this->request);
	}

	protected function closure(Closure $closure): mixed
	{
		return fn(RequestInterface $request) => $this->tryCatch(fn() => $closure($request), $request);
	}

	protected function middleware(Closure $closure, MiddlewareInterface $middleware): mixed
	{
		return fn(RequestInterface $request) => $this->tryCatch(fn() => $middleware($request, $closure), $request);
	}

	protected function tryCatch(Closure $closure, RequestInterface $request): mixed
	{
		try {
			return $closure();
		} catch (Throwable $throwable) {
			if ($this->closure === null) {
				throw $throwable;
			}

			return ($this->closure)($request, $throwable);
		}
	}
}
