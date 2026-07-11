<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\MediaType;

use Hoo\WordPressPluginFramework\Http\Semantics\Parameter\ParameterInterface;

readonly class MediaType implements MediaTypeInterface
{
	public function __construct(
		protected string $type,
		protected string $subtype,
		protected array $parameters,
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
}
