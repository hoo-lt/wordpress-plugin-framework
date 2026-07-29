<?php

namespace Hoo\WordPressPluginFramework\Hooker\Hooks;

use Closure;
use Hoo\WordPressPluginFramework\{
	Pipeline\PipelineFactoryInterface,
	Pipeline\Middlewares\MiddlewaresFactoryInterface,
};

readonly class HooksBuilder implements HooksBuilderInterface
{
	public function __construct(
		protected PipelineFactoryInterface $pipelineFactory,
		protected MiddlewaresFactoryInterface $middlewaresFactory,
		protected array $hooks = [],
	) {
	}

	public function hooks(): array
	{
		return $this->hooks;
	}

	public function withHooks(HookInterface ...$hooks): static
	{
		return new static($this->pipelineFactory, $this->middlewaresFactory, $hooks);
	}

	public function withoutHooks(): static
	{
		return new static($this->pipelineFactory, $this->middlewaresFactory, []);
	}

	public function withHook(HookInterface $hook): static
	{
		return $this->withHooks(
			...[
				...$this->hooks,
				$hook,
			],
		);
	}

	public function action(string $name, Closure $closure, int $priority = 10, ?Closure $middlewaresClosure = null): static
	{
		$pipelineFactory = $this->pipelineFactory($middlewaresClosure);

		return $this->withHook(
			new Action\Hook($pipelineFactory, $name, $closure, $priority),
		);
	}

	public function filter(string $name, Closure $closure, int $priority = 10, ?Closure $middlewaresClosure = null): static
	{
		$pipelineFactory = $this->pipelineFactory($middlewaresClosure);

		return $this->withHook(
			new Filter\Hook($pipelineFactory, $name, $closure, $priority),
		);
	}

	public function activation(string $file, Closure $closure, ?Closure $middlewaresClosure = null): static
	{
		$pipelineFactory = $this->pipelineFactory($middlewaresClosure);

		return $this->withHook(
			new Activation\Hook($pipelineFactory, $file, $closure),
		);
	}

	public function deactivation(string $file, Closure $closure, ?Closure $middlewaresClosure = null): static
	{
		$pipelineFactory = $this->pipelineFactory($middlewaresClosure);

		return $this->withHook(
			new Deactivation\Hook($pipelineFactory, $file, $closure),
		);
	}

	public function build(): array
	{
		return $this->hooks;
	}

	protected function pipelineFactory(?Closure $closure): PipelineFactoryInterface
	{
		return $this->pipelineFactory
			->withMiddlewares(
				$this->middlewaresFactory->tryCreate($closure),
			);
	}
}
