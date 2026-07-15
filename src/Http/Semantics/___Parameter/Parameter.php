<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Parameter;

readonly class Parameter implements ParameterInterface
{
	public function __construct(
		protected string $name,
		protected string $value,
	) {
	}

	public function name(): string
	{
		return $this->name;
	}

	public function value(): string
	{
		return $this->value;
	}
}
