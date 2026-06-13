<?php

namespace Hoo\WordPressPluginFramework\Hooker;

use Closure;
use Hoo\WordPressPluginFramework\Hooker\Hooks\HookInterface;

interface HookerInterface
{
	public function hooks(): array;
	public function withHooks(HookInterface ...$hooks): static;
	public function withoutHooks(): static;

	public function withHook(HookInterface $hook): static;

	public function action(string $name, Closure $closure, int $priority = 10, ?Closure $middlewaresBuilderClosure = null): static;
	public function filter(string $name, Closure $closure, int $priority = 10, ?Closure $middlewaresBuilderClosure = null): static;
	public function activation(string $file, Closure $closure, ?Closure $middlewaresBuilderClosure = null): static;
	public function deactivation(string $file, Closure $closure, ?Closure $middlewaresBuilderClosure = null): static;

	public function __invoke(): void;
}
