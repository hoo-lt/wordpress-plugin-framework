<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Type;

readonly class Type implements TypeInterface
{
	public function __construct(
		protected string $type,
	) {
	}

	public function __toString(): string
	{
		return $this->type;
	}
}
