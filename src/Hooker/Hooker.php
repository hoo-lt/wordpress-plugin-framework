<?php

namespace Hoo\WordPressPluginFramework\Hooker;

use Hoo\WordPressPluginFramework\Hooker\Hooks\HookInterface;

readonly class Hooker implements HookerInterface
{
	public function __construct(
		protected array $hooks = [],
	) {
	}

	public function hooks(): array
	{
		return $this->hooks;
	}

	public function withHooks(HookInterface ...$hooks): static
	{
		return new static($hooks);
	}

	public function withoutHooks(): static
	{
		return new static([]);
	}

	public function withHook(HookInterface $hook): static
	{
		return $this->withHooks(...$this->hooks, $hook);
	}

	public function __invoke(): void
	{
		foreach ($this->hooks as $hook) {
			$hook();
		}
	}
}
