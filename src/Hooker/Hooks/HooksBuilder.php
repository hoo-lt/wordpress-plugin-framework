<?php

namespace Hoo\WordPressPluginFramework\Hooker\Hooks;

use Closure;
use Hoo\WordPressPluginFramework\{
	Pipeline\PipelineInterface,
	Pipeline\Middlewares\MiddlewaresFactory,
};

readonly class HooksBuilder implements HooksBuilderInterface
{
	public function __construct(
		protected PipelineInterface $pipeline,
		protected MiddlewaresFactory $middlewaresFactory,
		protected array $hooks = [],
	) {
	}

	public function hooks(): array
	{
		return $this->hooks;
	}

	public function withHooks(HookInterface ...$hooks): static
	{
		return new static($this->pipeline, $this->middlewaresFactory, $hooks);
	}

	public function withoutHooks(): static
	{
		return new static($this->pipeline, $this->middlewaresFactory, []);
	}

	public function withHook(HookInterface $hook): static
	{
		return $this->withHooks(...$this->hooks, $hook);
	}

	public function action(string $name, Closure $closure, int $priority = 10, ?Closure $middlewaresClosure = null): static
	{
		$middlewares = $this->middlewaresFactory->tryCreate($middlewaresClosure);

		return $this->withHook(
			new Action\Hook($this->pipeline, $name, $closure, $priority, $middlewares),
		);
	}

	public function filter(string $name, Closure $closure, int $priority = 10, ?Closure $middlewaresClosure = null): static
	{
		$middlewares = $this->middlewaresFactory->tryCreate($middlewaresClosure);

		return $this->withHook(
			new Filter\Hook($this->pipeline, $name, $closure, $priority, $middlewares),
		);
	}

	public function activation(string $file, Closure $closure, ?Closure $middlewaresClosure = null): static
	{
		$middlewares = $this->middlewaresFactory->tryCreate($middlewaresClosure);

		return $this->withHook(
			new Activation\Hook($this->pipeline, $file, $closure, $middlewares),
		);
	}

	public function deactivation(string $file, Closure $closure, ?Closure $middlewaresClosure = null): static
	{
		$middlewares = $this->middlewaresFactory->tryCreate($middlewaresClosure);

		return $this->withHook(
			new Deactivation\Hook($this->pipeline, $file, $closure, $middlewares),
		);
	}

	public function build(): array
	{
		return $this->hooks;
	}
}
