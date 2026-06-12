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
		protected ?Closure $closure = null,
	) {
	}

	public function withRequest(RequestInterface $request): static
	{
		return new static($request, $this->middlewares, $this->closure);
	}

	public function withMiddlewares(MiddlewareInterface ...$middlewares): static
	{
		return new static($this->request, $middlewares, $this->closure);
	}

	public function withoutMiddlewares(): static
	{
		return new static($this->request, [], $this->closure);
	}

	public function withMiddleware(MiddlewareInterface $middleware): static
	{
		return $this->withMiddlewares(...$this->middlewares, $middleware);
	}

	public function catch(Closure $closure): static
	{
		return new static($this->request, $this->middlewares, $closure);
	}

	public function __invoke(Closure $closure): mixed
	{
		return array_reduce(array_reverse($this->middlewares), fn(Closure $closure, MiddlewareInterface $middleware) => fn(RequestInterface $request) => $this->tryCatch(fn() => $middleware($request, $closure), $request), fn(RequestInterface $request) => $this->tryCatch(fn() => $closure($request), $request))($this->request);
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