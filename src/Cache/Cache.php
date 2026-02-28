<?php

namespace Hoo\WordPressPluginFramework\Cache;

class Cache implements CacheInterface
{
	public function __construct(
		protected readonly int $ttl = 86400,
	) {
	}

	public function get(string $key): ?array
	{
		return get_transient($key) ?: null;
	}

	public function set(string $key, array $value): void
	{
		set_transient($key, $value, $this->ttl);
	}
}