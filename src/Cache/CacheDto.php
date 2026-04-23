<?php

namespace Hoo\WordPressPluginFramework\Cache;

readonly class CacheDto
{
	public function __construct(
		public mixed $value,
	) {
	}
}
