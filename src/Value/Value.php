<?php

namespace Hoo\WordPressPluginFramework\Value;

readonly class Value
{
	public function __construct(
		public mixed $value,
	) {
	}
}
