<?php

namespace Hoo\WordPressPluginFramework\Database;

use Hoo\WordPressPluginFramework\Database\Queries\QueryInterface;
use wpdb;

readonly class Database
{
	public function __construct(
		protected Select\SelectInterface $select,
		protected wpdb $wpdb,
	) {
	}

	public function select(QueryInterface $query): array
	{
		return ($this->select)($query);
	}
}