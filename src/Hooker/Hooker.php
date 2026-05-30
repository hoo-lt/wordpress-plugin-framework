<?php

namespace Hoo\WordPressPluginFramework\Hooker;

use Hoo\WordPressPluginFramework\Hooker\Hooks\HookInterface;

readonly class Hooker implements HookerInterface
{
	public function __construct(
		protected array $hooks = [],
	) {
	}

	public function withHooks(HookInterface ...$hooks): static
	{
		return new static($hooks);
	}

	public function __invoke(): void
	{
		foreach ($this->hooks as $hook) {
			$hook();
		}
	}
}
