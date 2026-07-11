<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\MediaRanges\MediaRange;

use Hoo\WordPressPluginFramework\Http\Semantics\Parameter\ParameterInterface;

readonly class MediaRange implements MediaRangeInterface
{
	public function __construct(
		protected string $type,
		protected string $subtype,
		protected array $parameters,
		protected float $weight,
	) {
	}

	public function type(): string
	{
		return $this->type;
	}

	public function subtype(): string
	{
		return $this->subtype;
	}

	public function parameters(): array
	{
		return $this->parameters;
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

	public function weight(): float
	{
		return $this->weight;
	}
}
