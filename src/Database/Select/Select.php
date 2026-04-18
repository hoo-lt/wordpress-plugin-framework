<?php

namespace Hoo\WordPressPluginFramework\Database\Select;

use Hoo\WordPressPluginFramework\Database\Queries;
use wpdb;

readonly class Select implements SelectInterface
{
	public function __construct(
		protected wpdb $wpdb,
	) {
		/*
		$this->wpdb->query(
			"SET SESSION group_concat_max_len = 1048576"
		);

		if ($this->wpdb->last_error) {
			throw new SelectException($this->wpdb->last_error);
		}
		*/
	}

	public function __invoke(Queries\Select\QueryInterface $query): array
	{
		$this->wpdb->query($query);

		if ($this->wpdb->last_error) {
			throw new SelectException($this->wpdb->last_error);
		}

		return array_map(get_object_vars(...), $this->wpdb->last_result);
	}
}