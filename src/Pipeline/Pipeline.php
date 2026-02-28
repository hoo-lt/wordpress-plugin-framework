<?php

namespace Hoo\WordpressPluginFramework\Pipeline;

use Hoo\WordpressPluginFramework\Middleware;

use Psr\Container\ContainerInterface;

class Pipeline implements PipelineInterface
{
	protected object $object;

	protected array $middlewares = [];

	public function __construct(
		protected readonly ContainerInterface $container,
	) {
	}

	public function object(object $object): self
	{
		$clone = clone $this;
		$clone->object = $object;

		return $clone;
	}

	public function middlewares(string ...$middlewares): self
	{
		$clone = clone $this;

		foreach ($middlewares as $middleware) {
			$middleware = $this->container->get($middleware);
			if (!$middleware instanceof Middleware\MiddlewareInterface) {
				//throw there
			}

			$clone->middlewares[] = $middleware;
		}

		return $clone;
	}

	public function __invoke(callable $callable): mixed
	{
		if (!$this->object) {
			//throw there
		}

		return array_reduce(array_reverse($this->middlewares), fn($callable, $middleware) => fn($object) => $middleware($object, $callable), $callable)($this->object);
	}
}