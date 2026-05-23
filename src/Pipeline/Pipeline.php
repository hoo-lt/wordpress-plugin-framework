<?php

namespace Hoo\WordPressPluginFramework\Pipeline;

use Closure;
use Hoo\WordPressPluginFramework\{
	Http\Exceptions\Exception,
	Http\Request\RequestInterface,
	Pipeline\Middlewares\MiddlewareInterface,
};
use Throwable;

readonly class Pipeline implements PipelineInterface
{
	public function __construct(
		protected RequestInterface $request,
		protected array $middlewares = [],
		protected ?Closure $catchException = null,
		protected ?Closure $catchThrowable = null,
	) {
	}

	public function withMiddlewares(MiddlewareInterface ...$middlewares): static
	{
		return new self(
			$this->request,
			$middlewares,
			$this->catchException,
			$this->catchThrowable,
		);
	}

	public function catchException(Closure $closure): static
	{
		return new self(
			$this->request,
			$this->middlewares,
			$closure,
			$this->catchThrowable,
		);
	}

	public function catchThrowable(Closure $closure): static
	{
		return new self(
			$this->request,
			$this->middlewares,
			$this->catchException,
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
					} catch (Exception $exception) {
						if ($this->catchException === null) {
							throw $exception;
						}

						return ($this->catchException)($request, $exception);
					} catch (Throwable $throwable) {
						if ($this->catchThrowable === null) {
							throw $throwable;
						}

						return ($this->catchThrowable)($request, $throwable);
					}
				};
			},
			function (RequestInterface $request) use ($closure) {
				try {
					return $closure($request);
				} catch (Exception $exception) {
					if ($this->catchException === null) {
						throw $exception;
					}

					return ($this->catchException)($request, $exception);
				} catch (Throwable $throwable) {
					if ($this->catchThrowable === null) {
						throw $throwable;
					}

					return ($this->catchThrowable)($request, $throwable);
				}
			},
		)($this->request);
	}
}