<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\MediaRanges\MediaRange;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\Parameters\Parameter\ParameterInterface,
	Http\Semantics\Parameters\ParametersInterface,
};

readonly class MediaRange implements MediaRangeInterface
{
	public function __construct(
		protected string $type,
		protected string $subtype,
		protected ParametersInterface $parameters,
		protected ?float $weight = null,   // null = no q on the wire; float = q was present (RFC 9110 §12.4.2)
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

	public function parameters(): ParametersInterface
	{
		return $this->parameters;
	}

	public function parameter(string $name): ?ParameterInterface
	{
		return $this->parameters->parameter($name);
	}

	public function weight(): ?float
	{
		return $this->weight;
	}
}
