<?php

namespace Hoo\WordPressPluginFramework\Hooker\Hooks\Action;

use Closure;
use Hoo\WordPressPluginFramework\{
	Hooker\Hooks\HookInterface,
	Http\Server\Request\RequestInterface,
	Pipeline\PipelineInterface,
	Pipeline\Middlewares\MiddlewaresInterface,
};

readonly class Hook implements HookInterface
{
	public function __construct(
		protected PipelineInterface $pipeline,
		protected string $name,
		protected Closure $closure,
		protected int $priority,
		protected ?MiddlewaresInterface $middlewares,
	) {
	}

	public function closure(): Closure
	{
		return $this->closure;
	}

	public function __invoke(): void
	{
		add_action(
			$this->name,
			fn(mixed ...$args) => $this->pipeline
				->withMiddlewares($this->middlewares)
			(fn(RequestInterface $request) => ($this->closure)($request, ...$args)),
			$this->priority,
			PHP_INT_MAX,
		);
	}
}
