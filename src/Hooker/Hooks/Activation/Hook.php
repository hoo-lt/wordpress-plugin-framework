<?php

namespace Hoo\WordPressPluginFramework\Hooker\Hooks\Activation;

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
		protected string $file,
		protected Closure $closure,
		protected array $middlewares = [],
	) {
	}

	public function withMiddlewares(MiddlewareInterface ...$middlewares): static
	{
		return new self(
			$this->pipeline,
			$this->file,
			$this->closure,
			$middlewares
		);
	}

	public function closure(): Closure
	{
		return $this->closure;
	}

	public function __invoke(): void
	{
		register_activation_hook(
			$this->file,
			fn(mixed ...$args) => $this->pipeline
				->withMiddlewares(...$this->middlewares)
			(fn(RequestInterface $request) => ($this->closure)($request, ...$args)),
		);
	}
}
