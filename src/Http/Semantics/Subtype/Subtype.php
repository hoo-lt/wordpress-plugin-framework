<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Subtype;

readonly class Subtype implements SubtypeInterface
{
	public function __construct(
		protected string $subtype,
	) {
	}

	public function __toString(): string
	{
		return $this->subtype;
	}
}
