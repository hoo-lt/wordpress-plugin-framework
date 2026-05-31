<?php

namespace Hoo\WordPressPluginFramework\Hooker\Hooks;

use Closure;
use Hoo\WordPressPluginFramework\Pipeline\PipelineInterface;

readonly class HookFactory implements HookFactoryInterface
{
	public function __construct(
		protected PipelineInterface $pipeline,
	) {
	}

	public function action(string $name, Closure $closure, int $priority = 10): HookInterface
	{
		return new Action\Hook($this->pipeline, $name, $closure, $priority);
	}

	public function filter(string $name, Closure $closure, int $priority = 10): HookInterface
	{
		return new Filter\Hook($this->pipeline, $name, $closure, $priority);
	}

	public function activation(string $file, Closure $closure): HookInterface
	{
		return new Activation\Hook($this->pipeline, $file, $closure);
	}

	public function deactivation(string $file, Closure $closure): HookInterface
	{
		return new Deactivation\Hook($this->pipeline, $file, $closure);
	}
}
