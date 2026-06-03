<?php

namespace Hoo\WordPressPluginFramework\Hooker\Hooks;

use Closure;
use Hoo\WordPressPluginFramework\{
	Pipeline\PipelineInterface,
	Pipeline\Middlewares\MiddlewaresBuilder,
};

readonly class HookFactory implements HookFactoryInterface
{
	public function __construct(
		protected PipelineInterface $pipeline,
		protected MiddlewaresBuilder $middlewaresBuilder,
	) {
	}

	public function action(string $name, Closure $closure, int $priority = 10, ?Closure $middlewaresBuilderClosure = null): HookInterface
	{
		return new Action\Hook(
			$this->pipeline,
			$name,
			$closure,
			$priority,
			$this->tryBuildMiddlewares($middlewaresBuilderClosure),
		);
	}

	public function filter(string $name, Closure $closure, int $priority = 10, ?Closure $middlewaresBuilderClosure = null): HookInterface
	{
		return new Filter\Hook(
			$this->pipeline,
			$name,
			$closure,
			$priority,
			$this->tryBuildMiddlewares($middlewaresBuilderClosure),
		);
	}

	public function activation(string $file, Closure $closure, ?Closure $middlewaresBuilderClosure = null): HookInterface
	{
		return new Activation\Hook(
			$this->pipeline,
			$file,
			$closure,
			$this->tryBuildMiddlewares($middlewaresBuilderClosure),
		);
	}

	public function deactivation(string $file, Closure $closure, ?Closure $middlewaresBuilderClosure = null): HookInterface
	{
		return new Deactivation\Hook(
			$this->pipeline,
			$file,
			$closure,
			$this->tryBuildMiddlewares($middlewaresBuilderClosure),
		);
	}

	protected function buildMiddlewares(Closure $closures): array
	{
		$middlewaresBuilder = $closures($this->middlewaresBuilder);
		if (!$middlewaresBuilder instanceof MiddlewaresBuilder) {
			//throw there
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
