<?php

namespace Hoo\WordPressPluginFramework\Hooker\Hooks\Filter;

use Closure;
use Hoo\WordPressPluginFramework\{
	Hooker\Hooks\HookInterface,
	Pipeline\PipelineFactoryInterface,
};

readonly class Hook implements HookInterface
{
	public function __construct(
		protected PipelineFactoryInterface $pipelineFactory,
		protected string $name,
		protected Closure $closure,
		protected int $priority = 10,
	) {
	}

	public function __invoke(): void
	{
		add_filter(
			$this->name,
			$this->callback(...),
			$this->priority,
			PHP_INT_MAX,
		);
	}

	protected function callback(...$args): mixed
	{
		$pipeline = $this->pipelineFactory->createFromServer();

		return $pipeline(fn($request) => ($this->closure)($request, ...$args));
	}
}
