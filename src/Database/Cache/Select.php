<?php

namespace Hoo\WordPressPluginFramework\Database\Cache;

use Hoo\WordPressPluginFramework\Cache\CacheInterface;
use Hoo\WordPressPluginFramework\Database\Select\SelectInterface;
use Hoo\WordPressPluginFramework\Database\Queries;

readonly class Select implements SelectInterface
{
	public function __construct(
		protected CacheInterface $cache,
		protected SelectInterface $select,
		protected string $salt,
	) {
	}

	public function __invoke(Queries\Select\QueryInterface $query): array
	{
		return $this->cache->remember(hash_hmac('sha256', $query, $this->salt), fn() => ($this->select)($query));
	}
}