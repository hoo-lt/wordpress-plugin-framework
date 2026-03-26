<?php

namespace Hoo\WordPressPluginFramework\Database;

use wpdb;

class Database implements DatabaseInterface
{
	public function __construct(
		protected readonly wpdb $wpdb
	) {
		$this->wpdb->query(
			"SET SESSION group_concat_max_len = 1048576"
		);
	}

	public function select(Query\Select\QueryInterface $query): array
	{
		$results = $this->wpdb->get_results($query(), ARRAY_A);
		if ($this->wpdb->last_error) {
			throw new DatabaseException('invalid query');
		}

		if ($results === null) {
			return [];
		}

		return $results;
	}

	public function json(Query\Select\QueryInterface $query): array
	{
		$var = $this->wpdb->get_var($query());
		if ($this->wpdb->last_error) {
			throw new DatabaseException('invalid query');
		}

		if ($var === null) {
			return [];
		}

		$json = json_decode($var, true);
		if ($json === null) {
			throw new DatabaseException('error decoding json');
		}

		return $json;
	}
}