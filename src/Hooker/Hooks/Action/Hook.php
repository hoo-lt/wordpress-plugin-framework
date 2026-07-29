<?php

namespace Hoo\WordPressPluginFramework\Hooker\Hooks\Action;

use Closure;
use Hoo\WordPressPluginFramework\{
	Hooker\Hooks\HookInterface,
	Pipeline\PipelineInterface,
};

readonly class Hook implements HookInterface
{
	public function __construct(
		protected PipelineInterface $pipeline,
		protected string $name,
		protected Closure $closure,
		protected int $priority = 10,
	) {
	}

	public function __invoke(): void
	{
		add_action(
			$this->name,
			fn(...$args) => ($this->pipeline)(fn($request) => ($this->closure)($request, ...$args)),
			$this->priority,
			PHP_INT_MAX,
		);
	}
}
