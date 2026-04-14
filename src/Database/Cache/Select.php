<?php

namespace Hoo\WordPressPluginFramework\Database\Cache;

use Hoo\WordPressPluginFramework\Cache\CacheInterface;
use Hoo\WordPressPluginFramework\Database\SelectInterface;
use Hoo\WordPressPluginFramework\Database\Queries;

class Select implements SelectInterface
{
	public function __construct(
		protected readonly CacheInterface $cache,
		protected readonly SelectInterface $select,
	) {
	}

	public function __invoke(Queries\Select\QueryInterface $query): array
	{
		return $this->value($query, fn() => ($this->select)($query));
	}

	protected function key(Queries\Select\QueryInterface $query): string
	{
		return md5($query());
	}

	protected function value(Queries\Select\QueryInterface $query, callable $callable): array
	{
		$key = $this->key($query);

		$value = $this->cache->get($key);
		if ($value) {
			return $value;
		}

		$value = $callable();
		if ($value) {
			$this->cache->set($key, $value);
		}

		return $value;
	}
}