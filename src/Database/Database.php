<?php

namespace Hoo\WordPressPluginFramework\Database;

use wpdb;

class Database implements DatabaseInterface
{
	public function __construct(
		protected readonly wpdb $wpdb
	) {
	}

	public function select(Query\Select\QueryInterface $query): array
	{
		$results = $this->wpdb->get_results($query(), ARRAY_A);
		if (!$results) {
			throw new DatabaseException('invalid query');
		}

		return $results;
	}
}