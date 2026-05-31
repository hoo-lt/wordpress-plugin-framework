<?php

namespace Hoo\WordPressPluginFramework\Cache\Value;

readonly class Value
{
	public function __construct(
		protected mixed $value,
	) {
	}

	public function __invoke(): mixed
	{
		return $this->value;
	}
}
