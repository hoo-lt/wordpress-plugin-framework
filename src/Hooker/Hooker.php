<?php

namespace Hoo\WordPressPluginFramework\Hooker;

use Closure;
use Hoo\WordPressPluginFramework\{
	Hooker\Hooks\HookInterface,
	Hooker\Hooks\HookFactoryInterface,
};

readonly class Hooker implements HookerInterface
{
	public function __construct(
		protected HookFactoryInterface $hookFactory,
		protected array $hooks = [],
	) {
	}

	public function hooks(): array
	{
		return $this->hooks;
	}

	public function withHooks(HookInterface ...$hooks): static
	{
		return new static($this->hookFactory, $hooks);
	}

	public function withoutHooks(): static
	{
		return new static($this->hookFactory, []);
	}

	public function withHook(HookInterface $hook): static
	{
		return $this->withHooks(...$this->hooks, $hook);
	}

	public function action(string $name, Closure $closure, int $priority = 10, ?Closure $middlewaresBuilderClosure = null): static
	{
		return $this->withHook(
			$this->hookFactory->action($name, $closure, $priority, $middlewaresBuilderClosure),
		);
	}

	public function filter(string $name, Closure $closure, int $priority = 10, ?Closure $middlewaresBuilderClosure = null): static
	{
		return $this->withHook(
			$this->hookFactory->filter($name, $closure, $priority, $middlewaresBuilderClosure),
		);
	}

	public function activation(string $file, Closure $closure, ?Closure $middlewaresBuilderClosure = null): static
	{
		return $this->withHook(
			$this->hookFactory->activation($file, $closure, $middlewaresBuilderClosure),
		);
	}

	public function deactivation(string $file, Closure $closure, ?Closure $middlewaresBuilderClosure = null): static
	{
		return $this->withHook(
			$this->hookFactory->deactivation($file, $closure, $middlewaresBuilderClosure),
		);
	}

	public function __invoke(): void
	{
		foreach ($this->hooks as $hook) {
			$hook();
		}
	}
}
