<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\MediaType;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\Parameters\Parameter\ParameterInterface,
	Http\Semantics\Parameters\ParametersInterface,
};

readonly class MediaType implements MediaTypeInterface
{
	public function __construct(
		protected string $type,
		protected string $subtype,
		protected ParametersInterface $parameters,
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
}
