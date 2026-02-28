<?php

namespace Hoo\WordpressPluginFramework\Database\Cache;

use Hoo\WordpressPluginFramework\Cache\CacheInterface;
use Hoo\WordpressPluginFramework\Database\DatabaseInterface;

use Hoo\WordpressPluginFramework\Database\Query;

class Database implements DatabaseInterface
{
	public function __construct(
		protected readonly CacheInterface $cache,
		protected readonly DatabaseInterface $database,
	) {
	}

	public function select(Query\Select\QueryInterface $query): ?array
	{
		$key = $this->key($query);

		$value = $this->cache->get($key);
		if ($value) {
			return $value;
		}

		$value = $this->database->select($query);
		if ($value) {
			$this->cache->set($key, $value);
		}

		return $value;
	}

	protected function key(Query\QueryInterface $query): string
	{
		return md5($query());
	}
}