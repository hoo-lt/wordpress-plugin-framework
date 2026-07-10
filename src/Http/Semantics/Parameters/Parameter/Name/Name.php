<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Parameters\Parameter\Name;

readonly class Name implements NameInterface
{
	public function __construct(
		protected string $name,
	) {
	}

	public function __toString(): string
	{
		return $this->name;
	}
}
