<?php

namespace Hoo\WordPressPluginFramework\Cache;

class Cache implements CacheInterface
{
	public function __construct(
		protected readonly int $ttl = 86400,
	) {
	}

	public function remember(string $key, callable $callable): mixed
	{
		$value = get_transient($key);
		if ($value !== false) {
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