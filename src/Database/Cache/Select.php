<?php

namespace Hoo\WordPressPluginFramework\Database\Cache;

use Hoo\WordPressPluginFramework\Cache\CacheInterface;
use Hoo\WordPressPluginFramework\Database\SelectInterface;
use Hoo\WordPressPluginFramework\Database\Queries;

readonly class Select implements SelectInterface
{
	public function __construct(
		protected CacheInterface $cache,
		protected SelectInterface $select,
	) {
	}

	public function __invoke(Queries\Select\QueryInterface $query): array
	{
		return $this->cache->remember(md5($query), fn() => ($this->select)($query));
	}
}