<?php

namespace Hoo\WordPressPluginFramework\Pipeline;

use Closure;
use Hoo\WordPressPluginFramework\{
	Exceptions\Handler\HandlerInterface,
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
		protected ?HandlerInterface $handler = null,
	) {
	}

	public function __invoke(Closure $closure): mixed
	{
		if ($this->middlewares !== null) {
			$closure = array_reduce(
				array_reverse(
					iterator_to_array($this->middlewares),
				),
				fn(Closure $closure, MiddlewareInterface $middleware): Closure => fn(RequestInterface $request): mixed => $middleware($request, $closure),
				$closure,
			);
		}

		try {
			return $closure($this->request);
		} catch (Throwable $throwable) {
			if ($this->handler === null) {
				throw $throwable;
			}

			return $this->handler->handle($this->request, $throwable);
		}
	}
}
