<?php

namespace Hoo\WordPressPluginFramework\Hooker;

use Closure;
use Hoo\WordPressPluginFramework\{
	Hooker\Hooks\HookFactoryInterface,
	Hooker\Hooks\HookInterface,
};

readonly class Hooker implements HookerInterface
{
	public function __construct(
		protected HookFactoryInterface $hookFactory,
		protected array $hooks = [],
	) {
	}

	public function withHooks(HookInterface ...$hooks): static
	{
		return new static(
			$this->hookFactory,
			$hooks
		);
	}

	public function withActionHook(string $name, Closure $closure, int $priority = 10): static
	{
		return $this->withHooks(
			$this->hookFactory->action($name, $closure, $priority),
		);
	}

	public function withFilterHook(string $name, Closure $closure, int $priority = 10): static
	{
		return $this->withHooks(
			$this->hookFactory->filter($name, $closure, $priority),
		);
	}

	public function withActivationHook(string $file, Closure $closure): static
	{
		return $this->withHooks(
			$this->hookFactory->activation($file, $closure),
		);
	}

	public function withDeactivationHook(string $file, Closure $closure): static
	{
		return $this->withHooks(
			$this->hookFactory->deactivation($file, $closure),
		);
	}

	public function __invoke(): void
	{
		foreach ($this->hooks as $hook) {
			$hook();
		}
	}
}
