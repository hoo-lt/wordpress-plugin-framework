<?php

namespace Hoo\WordPressPluginFramework\Hooker\Hooks\Filter;

use Closure;
use Hoo\WordPressPluginFramework\{
	Hooker\Hooks\HookInterface,
	Http\Request\RequestInterface,
	Pipeline\PipelineInterface,
	Pipeline\Middlewares\MiddlewareInterface
};

readonly class Hook implements HookInterface
{
	public function __construct(
		protected PipelineInterface $pipeline,
		protected string $name,
		protected Closure $closure,
		protected int $priority = PHP_INT_MAX,
		protected array $middlewares = [],
	) {
	}

	public function withMiddlewares(MiddlewareInterface ...$middlewares): static
	{
		return new self(
			$this->pipeline,
			$this->name,
			$this->closure,
			$this->priority,
			$middlewares
		);
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
