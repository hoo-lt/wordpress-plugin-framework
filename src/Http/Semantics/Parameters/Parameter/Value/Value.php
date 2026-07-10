<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameter\Value;

readonly class Value implements ValueInterface
{
	public function __construct(
		protected string $value,
	) {
	}

	public function __toString(): string
	{
		return $this->value;
	}
}
