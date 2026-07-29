<?php

namespace Hoo\WordPressPluginFramework\Hooker\Hooks\Activation;

use Closure;
use Hoo\WordPressPluginFramework\{
	Hooker\Hooks\HookInterface,
	Pipeline\PipelineInterface,
};

readonly class Hook implements HookInterface
{
	public function __construct(
		protected PipelineInterface $pipeline,
		protected string $file,
		protected Closure $closure,
	) {
	}
	
	public function __invoke(): void
	{
		register_activation_hook(
			$this->file,
			fn(...$args) => ($this->pipeline)(fn($request) => ($this->closure)($request, ...$args)),
		);
	}
}
