<?php

namespace Hoo\WordPressPluginFramework\Hook\Activation;

use Closure;
use Hoo\WordPressPluginFramework\Hook\HookInterface;
use Hoo\WordPressPluginFramework\Middlewares\MiddlewareInterface;
use Hoo\WordPressPluginFramework\Pipeline\PipelineInterface;

readonly class Hook implements HookInterface
{
	public function __construct(
		protected PipelineInterface $pipeline,
		protected string $file,
		protected Closure $closure,
		protected array $middlewares = [],
	) {
	}

	public function withMiddlewares(MiddlewareInterface ...$middlewares): HookInterface
	{
		return new self(
			$this->pipeline,
			$this->file,
			$this->closure,
			$middlewares
		);
	}

	public function __invoke(): void
	{
		register_activation_hook(
			$this->file,
			fn(mixed ...$args) => $this->pipeline
				->withMiddlewares(...$this->middlewares)
			(fn() => ($this->closure)(...$args))
		);
	}
}
