<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Parameters;

use ArrayIterator;
use Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameter\ParameterInterface;
use Traversable;

readonly class Parameters implements ParametersInterface
{
	public function __construct(
		protected array $parameters,
	) {
	}

	public function parameter(string $name): ?ParameterInterface
	{
		foreach ($this->parameters as $parameter) {
			if ($parameter->name() === strtolower($name)) {
				return $parameter;
			}
		}

		return null;
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator($this->parameters);
	}

	public function count(): int
	{
		return count($this->parameters);
	}
}
