<?php

namespace Hoo\WordPressPluginFramework\Database;

use Hoo\WordPressPluginFramework\Json;
use wpdb;

readonly class Database implements DatabaseInterface
{
	public function __construct(
		protected wpdb $wpdb,
		protected Json\JsonInterface $json,
	) {
		/*
		$this->wpdb->query(
			"SET SESSION group_concat_max_len = 1048576"
		);
		*/
	}

	public function select(Query\Select\QueryInterface $query): array
	{
		$results = $this->wpdb->get_results($query(), ARRAY_A);

		if ($this->wpdb->last_error) {
			throw new DatabaseException($this->wpdb->last_error);
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
			throw new DatabaseException($this->wpdb->last_error);
		}

		if ($var === null) {
			return [];
		}

		return $this->json->decode($var);
	}
}