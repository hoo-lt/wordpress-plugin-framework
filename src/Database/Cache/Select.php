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
		return $this->cache->remember(md5($query), fn() => ($this->select)($query));
	}
}