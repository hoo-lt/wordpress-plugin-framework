<?php

namespace Hoo\WordPressPluginFramework\Hooker;

use Hoo\WordPressPluginFramework\Hooker\Hooks\HookInterface;

interface HookerInterface
{
	public function hooks(): array;
	public function withHooks(HookInterface ...$hooks): static;
	public function withoutHooks(): static;

	public function withHook(HookInterface $hook): static;

	public function __invoke(): void;
}
