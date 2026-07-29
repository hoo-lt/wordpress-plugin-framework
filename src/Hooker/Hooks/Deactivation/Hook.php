<?php

namespace Hoo\WordPressPluginFramework\Hooker\Hooks\Deactivation;

use Closure;
use Hoo\WordPressPluginFramework\{
	Hooker\Hooks\HookInterface,
	Pipeline\PipelineFactoryInterface,
};

readonly class Hook implements HookInterface
{
	public function __construct(
		protected PipelineFactoryInterface $pipelineFactory,
		protected string $file,
		protected Closure $closure,
	) {
	}

	public function __invoke(): void
	{
		register_deactivation_hook(
			$this->file,
			$this->callback(...),
		);
	}

	protected function callback(...$args): mixed
	{
		$pipeline = $this->pipelineFactory->createFromServer();

		return $pipeline(fn($request) => ($this->closure)($request, ...$args));
	}
}
