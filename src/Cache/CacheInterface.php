<?php

namespace Hoo\WordPressPluginFramework\Cache;

interface CacheInterface
{
	public function remember(string $key, callable $callable): mixed;
	public function forget(string $key): void;
}