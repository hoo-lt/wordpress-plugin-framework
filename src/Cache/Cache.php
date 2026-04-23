<?php

namespace Hoo\WordPressPluginFramework\Cache;

use Closure;

readonly class Cache implements CacheInterface
{
	public function __construct(
		protected int $ttl = 86400,
	) {
	}

	public function remember(string $key, Closure $closure): mixed
	{
		$cacheDto = get_transient($key);
		if ($cacheDto !== false) {
			if (!$cacheDto instanceof CacheDto) {
				throw new CacheException('corrupted cache value');
			}

			return $cacheDto->value;
		}

		$cacheDto = new CacheDto($closure());

		set_transient($key, $cacheDto, $this->ttl);

		return $cacheDto->value;
	}

	public function forget(string $key): void
	{
		delete_transient($key);
	}
}