<?php

namespace Hoo\WordPressPluginFramework\Cache;

use Closure;

readonly class Cache implements CacheInterface
{
	public function __construct(
		protected int $ttl,
	) {
	}

	public function remember(string $key, Closure $closure, ?int $ttl = null): mixed
	{
		$value = get_transient($key);
		if ($value !== false) {
			if (!$value instanceof Value\Value) {
				throw new CacheException('corrupted cache value');
			}

			return $value();
		}

		$value = new Value\Value($closure());

		set_transient($key, $value, $ttl ?? $this->ttl);

		return $value();
	}

	public function forget(string $key): void
	{
		delete_transient($key);
	}
}