<?php

namespace Hoo\WordPressPluginFramework\Hooker\Hooks\Deactivation;

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
		protected string $file,
		protected Closure $closure,
		protected array $middlewares = [],
	) {
	}

	public function middlewares(): array
	{
		return $this->middlewares;
	}

	public function withMiddlewares(MiddlewareInterface ...$middlewares): static
	{
		return new static($this->pipeline, $this->file, $this->closure, $middlewares);
	}

	public function withoutMiddlewares(): static
	{
		return new static($this->pipeline, $this->file, $this->closure, []);
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
		register_deactivation_hook(
			$this->file,
			fn(mixed ...$args) => $this->pipeline
				->withMiddlewares(...$this->middlewares)
			(fn(RequestInterface $request) => ($this->closure)($request, ...$args)),
		);
	}
}
