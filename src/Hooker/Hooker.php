<?php

namespace Hoo\WordPressPluginFramework\Hooker;

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

	public function __invoke(): void
	{
		foreach ($this->hooks as $hook) {
			$hook();
		}
	}
}
