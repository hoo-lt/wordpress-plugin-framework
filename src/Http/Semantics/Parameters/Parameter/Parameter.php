<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameter;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\Parameters\Parameter\Name\NameInterface,
	Http\Semantics\Parameters\Parameter\Value\ValueInterface,
};

readonly class Parameter implements ParameterInterface
{
	public function __construct(
		protected NameInterface $name,
		protected ValueInterface $value,
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
