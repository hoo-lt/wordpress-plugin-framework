<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares;

use Closure;

readonly class MiddlewaresFactory implements MiddlewaresFactoryInterface
{
	public function __construct(
		protected MiddlewaresBuilderInterface $middlewaresBuilder,
	) {
	}

	public function create(Closure $closure): MiddlewaresInterface
	{
		return new Middlewares($this->middlewaresBuilder, $closure);
	}

	public function tryCreate(?Closure $closure): ?MiddlewaresInterface
	{
		if ($closure === null) {
			return null;
		}

		return $this->create($closure);
	}
}
