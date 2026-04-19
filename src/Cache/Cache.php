<?php

namespace Hoo\WordPressPluginFramework\Cache;

readonly class Cache implements CacheInterface
{
	public function __construct(
		protected int $ttl = 86400,
	) {
	}

	public function remember(string $key, callable $callable): mixed
	{
		$value = get_transient($key);
		if ($value !== false) {
			if (
				!is_array($value) ||
				!array_key_exists('value', $value)
			) {
				throw new CacheException('corrupted cache value');
			}

			return $value['value'];
		}

		$value = [
			'value' => $callable(),
		];

		set_transient($key, $value, $this->ttl);

		return $value['value'];
	}

	public function forget(string $key): void
	{
		delete_transient($key);
	}
}