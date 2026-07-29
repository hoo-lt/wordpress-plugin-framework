<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares;

use ArrayIterator;
use Closure;
use Traversable;

readonly class Middlewares implements MiddlewaresInterface
{
	protected array $middlewares;

	public function __construct(
		protected MiddlewaresBuilderInterface $middlewaresBuilder,
		protected Closure $closure,
	) {
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator(
			$this->middlewares ??= $this->middlewares(),
		);
	}

	protected function middlewares(): array
	{
		$middlewaresBuilder = ($this->closure)($this->middlewaresBuilder);
		if (!$middlewaresBuilder instanceof MiddlewaresBuilderInterface) {
			throw new MiddlewaresBuilderException('The middlewares builder closure must return an instance of MiddlewaresBuilder.');
		}

		return $middlewaresBuilder->build();
	}
}
