<?php

namespace Hoo\WordPressPluginFramework\Database\Cache;

use Hoo\WordPressPluginFramework\Cache\CacheInterface;
use Hoo\WordPressPluginFramework\Database\DatabaseInterface;
use Hoo\WordPressPluginFramework\Database\Query;

class Database implements DatabaseInterface
{
	public function __construct(
		protected readonly CacheInterface $cache,
		protected readonly DatabaseInterface $database,
	) {
	}

	public function select(Query\Select\QueryInterface $query): array
	{
		return $this->value($query, fn() => $this->database->select($query));
	}

	public function json(Query\Select\QueryInterface $query): array
	{
		return $this->value($query, fn() => $this->database->json($query));
	}

	protected function key(Query\QueryInterface $query): string
	{
		return md5($query());
	}

	protected function value(Query\QueryInterface $query, callable $callable): array
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