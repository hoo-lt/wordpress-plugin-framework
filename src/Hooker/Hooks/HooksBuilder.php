<?php

namespace Hoo\WordPressPluginFramework\Hooker\Hooks;

use Closure;
use Hoo\WordPressPluginFramework\{
	Pipeline\PipelineInterface,
	Pipeline\Middlewares\MiddlewaresFactoryInterface,
};

readonly class HooksBuilder implements HooksBuilderInterface
{
	public function __construct(
		protected PipelineInterface $pipeline,
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
		$pipeline = $this->pipeline($middlewaresClosure);

		return $this->withHook(
			new Action\Hook($pipeline, $name, $closure, $priority),
		);
	}

	public function filter(string $name, Closure $closure, int $priority = 10, ?Closure $middlewaresClosure = null): static
	{
		$pipeline = $this->pipeline($middlewaresClosure);

		return $this->withHook(
			new Filter\Hook($pipeline, $name, $closure, $priority),
		);
	}

	public function activation(string $file, Closure $closure, ?Closure $middlewaresClosure = null): static
	{
		$pipeline = $this->pipeline($middlewaresClosure);

		return $this->withHook(
			new Activation\Hook($pipeline, $file, $closure),
		);
	}

	public function deactivation(string $file, Closure $closure, ?Closure $middlewaresClosure = null): static
	{
		$pipeline = $this->pipeline($middlewaresClosure);

		return $this->withHook(
			new Deactivation\Hook($pipeline, $file, $closure),
		);
	}

	public function build(): array
	{
		return $this->hooks;
	}

	protected function pipeline(?Closure $closure): PipelineInterface
	{
		$middlewares = $this->middlewaresFactory->tryCreate($closure);
		return $middlewares !== null
			? $this->pipeline
				->withMiddlewares($middlewares)
			: $this->pipeline
				->withoutMiddlewares();
	}
}
