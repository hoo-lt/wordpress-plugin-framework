<?php

namespace Hoo\WordPressPluginFramework\Hooker;

use Closure;
use Hoo\WordPressPluginFramework\Hooker\Hooks\HookInterface;

interface HookerInterface
{
	public function withHooks(HookInterface ...$hooks): static;

	public function withActionHook(string $name, Closure $closure, int $priority = 10): static;
	public function withFilterHook(string $name, Closure $closure, int $priority = 10): static;

	public function withActivationHook(string $file, Closure $closure): static;
	public function withDeactivationHook(string $file, Closure $closure): static;

	public function __invoke(): void;
}
