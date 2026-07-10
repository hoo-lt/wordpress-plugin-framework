<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\MediaRanges\MediaRange;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\Parameters\Parameter\ParameterInterface,
	Http\Semantics\Parameters\ParametersInterface,
	Http\Semantics\Subtype\SubtypeInterface,
	Http\Semantics\Type\TypeInterface,
	Http\Semantics\Weight\WeightInterface,
};

readonly class MediaRange implements MediaRangeInterface
{
	public function __construct(
		protected TypeInterface $type,
		protected SubtypeInterface $subtype,
		protected ParametersInterface $parameters,
		protected ?WeightInterface $weight = null,
	) {
	}

	public function type(): string
	{
		return (string) $this->type;
	}

	public function subtype(): string
	{
		return (string) $this->subtype;
	}

	public function parameters(): ParametersInterface
	{
		return $this->parameters;
	}

	public function parameter(string $name): ?ParameterInterface
	{
		return $this->parameters->parameter($name);
	}

	public function weight(): ?WeightInterface
	{
		return $this->weight;
	}
}
