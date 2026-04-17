<?php

namespace Hoo\WordPressPluginFramework\Hook\Filter;

use Closure;
use Hoo\WordPressPluginFramework\Hook\HookInterface;
use Hoo\WordPressPluginFramework\Middlewares\MiddlewareInterface;
use Hoo\WordPressPluginFramework\Pipeline\PipelineInterface;

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

	public function withMiddlewares(MiddlewareInterface ...$middlewares): HookInterface
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
			(fn() => ($this->closure)(...$args)),
			$this->priority,
			PHP_INT_MAX
		);
	}
}
