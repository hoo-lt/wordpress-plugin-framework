<?php

namespace Hoo\WordPressPluginFramework\Pipeline;

use Closure;
use Hoo\WordPressPluginFramework\Http\Request\RequestInterface;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\MiddlewareInterface;
use Hoo\WordPressPluginFramework\Http;
use Throwable;

readonly class Pipeline implements PipelineInterface
{
	public function __construct(
		protected RequestInterface $request,
		protected array $middlewares = [],
		protected ?Closure $catchExceptionClosure = null,
		protected ?Closure $catchThrowableClosure = null,
	) {
	}

	public function withMiddlewares(MiddlewareInterface ...$middlewares): static
	{
		return new self(
			$this->request,
			$middlewares,
			$this->catchExceptionClosure,
			$this->catchThrowableClosure,
		);
	}

	public function catchException(Closure $catchExceptionClosure): static
	{
		return new self(
			$this->request,
			$this->middlewares,
			$catchExceptionClosure,
			$this->catchThrowableClosure,
		);
	}

	public function catchThrowable(Closure $catchThrowableClosure): static
	{
		return new self(
			$this->request,
			$this->middlewares,
			$this->catchExceptionClosure,
			$catchThrowableClosure,
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
					} catch (Http\Exceptions\Exception $exception) {
						if ($this->catchExceptionClosure === null) {
							throw $exception;
						}

						return ($this->catchExceptionClosure)($request, $exception);
					} catch (Throwable $throwable) {
						if ($this->catchThrowableClosure === null) {
							throw $throwable;
						}

						return ($this->catchThrowableClosure)($request, $throwable);
					}
				};
			},
			function (RequestInterface $request) use ($closure) {
				try {
					return $closure($request);
				} catch (Http\Exceptions\Exception $exception) {
					if ($this->catchExceptionClosure === null) {
						throw $exception;
					}

					return ($this->catchExceptionClosure)($request, $exception);
				} catch (Throwable $throwable) {
					if ($this->catchThrowableClosure === null) {
						throw $throwable;
					}

					return ($this->catchThrowableClosure)($request, $throwable);
				}
			},
		)($this->request);
	}
}