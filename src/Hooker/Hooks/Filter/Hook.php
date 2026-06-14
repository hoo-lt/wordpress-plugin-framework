<?php

namespace Hoo\WordPressPluginFramework\Hooker\Hooks\Filter;

use Closure;
use Hoo\WordPressPluginFramework\{
	Hooker\Hooks\HookInterface,
	Http\Server\Request\RequestInterface,
	Pipeline\PipelineInterface,
	Pipeline\Middlewares\MiddlewareInterface
};

readonly class Hook implements HookInterface
{
	public function __construct(
		protected PipelineInterface $pipeline,
		protected string $name,
		protected Closure $closure,
		protected int $priority = 10,
		protected array $middlewares = [],
	) {
	}

	public function middlewares(): array
	{
		return $this->middlewares;
	}

	public function withMiddlewares(MiddlewareInterface ...$middlewares): static
	{
		return new static($this->pipeline, $this->name, $this->closure, $this->priority, $middlewares);
	}

	public function withoutMiddlewares(): static
	{
		return new static($this->pipeline, $this->name, $this->closure, $this->priority, []);
	}

	public function withMiddleware(MiddlewareInterface $middleware): static
	{
		return $this->withMiddlewares(...$this->middlewares, $middleware);
	}

	public function closure(): Closure
	{
		return $this->closure;
	}

	public function __invoke(): void
	{
		add_filter(
			$this->name,
			fn(mixed ...$args) => $this->pipeline
				->withMiddlewares(...$this->middlewares)
			(fn(RequestInterface $request) => ($this->closure)($request, ...$args)),
			$this->priority,
			PHP_INT_MAX
		);
	}
}
