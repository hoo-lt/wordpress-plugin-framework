<?php

namespace Hoo\WordPressPluginFramework\Cache;

interface CacheInterface
{
	public function get(string $key): ?array;
	public function set(string $key, array $value): void;
}