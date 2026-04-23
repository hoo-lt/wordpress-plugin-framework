<?php

namespace Hoo\WordPressPluginFramework\Cache;

use Closure;

interface CacheInterface
{
	public function remember(string $key, Closure $closure): mixed;
	public function forget(string $key): void;
}