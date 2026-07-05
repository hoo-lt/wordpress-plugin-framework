<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\MediaType;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\Parameters\Parameter\ParameterInterface,
	Http\Semantics\Token\TokenInterface,
};

readonly class MediaType implements MediaTypeInterface
{
	protected array $parameters;

	public function __construct(
		protected TokenInterface $type,
		protected TokenInterface $subtype,
		ParameterInterface ...$parameters,
	) {
		$this->parameters = $parameters;
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
		$name = strtolower($name);

		foreach ($this->parameters as $parameter) {
			if ($parameter->name() === $name) {
				return $parameter;
			}
		}

		return null;
	}

	public function charset(): ?string
	{
		return $this->parameter('charset')?->value();
	}

	public function __toString(): string
	{
		$mediaType = "{$this->type}/{$this->subtype}";

		foreach ($this->parameters as $parameter) {
			$mediaType .= "; {$parameter}";
		}

		return $mediaType;
	}
}
