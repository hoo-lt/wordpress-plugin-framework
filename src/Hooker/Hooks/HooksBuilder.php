<?php

namespace Hoo\WordPressPluginFramework\Hooker\Hooks;

use Closure;
use Hoo\WordPressPluginFramework\{
	Pipeline\PipelineInterface,
	Pipeline\Middlewares\MiddlewaresBuilder,
};

readonly class HooksBuilder implements HooksBuilderInterface
{
	public function __construct(
		protected PipelineInterface $pipeline,
		protected MiddlewaresBuilder $middlewaresBuilder,
		protected array $hooks = [],
	) {
	}

	public function hooks(): array
	{
		return $this->hooks;
	}

	public function withHooks(HookInterface ...$hooks): static
	{
		return new static($this->pipeline, $this->middlewaresBuilder, $hooks);
	}

	public function withoutHooks(): static
	{
		return new static($this->pipeline, $this->middlewaresBuilder, []);
	}

	public function withHook(HookInterface $hook): static
	{
		return $this->withHooks(...$this->hooks, $hook);
	}

	public function action(string $name, Closure $closure, int $priority = 10, ?Closure $middlewaresBuilderClosure = null): static
	{
		$middlewares = $this->tryBuildMiddlewares($middlewaresBuilderClosure);

		return $this->withHook(
			new Action\Hook($this->pipeline, $name, $closure, $priority, $middlewares),
		);
	}

	public function filter(string $name, Closure $closure, int $priority = 10, ?Closure $middlewaresBuilderClosure = null): static
	{
		$middlewares = $this->tryBuildMiddlewares($middlewaresBuilderClosure);

		return $this->withHook(
			new Filter\Hook($this->pipeline, $name, $closure, $priority, $middlewares),
		);
	}

	public function activation(string $file, Closure $closure, ?Closure $middlewaresBuilderClosure = null): static
	{
		$middlewares = $this->tryBuildMiddlewares($middlewaresBuilderClosure);

		return $this->withHook(
			new Activation\Hook($this->pipeline, $file, $closure, $middlewares),
		);
	}

	public function deactivation(string $file, Closure $closure, ?Closure $middlewaresBuilderClosure = null): static
	{
		$middlewares = $this->tryBuildMiddlewares($middlewaresBuilderClosure);

		return $this->withHook(
			new Deactivation\Hook($this->pipeline, $file, $closure, $middlewares),
		);
	}

	public function build(): array
	{
		return $this->hooks;
	}

	protected function buildMiddlewares(Closure $closures): array
	{
		$middlewaresBuilder = $closures($this->middlewaresBuilder);
		if (!$middlewaresBuilder instanceof MiddlewaresBuilder) {
			throw new HooksBuilderException('The middlewares builder closure must return an instance of MiddlewaresBuilder.');
		}

		return $middlewaresBuilder->build();
	}

	protected function tryBuildMiddlewares(?Closure $closures): array
	{
		if ($closures === null) {
			return [];
		}

		return $this->buildMiddlewares($closures);
	}
}
