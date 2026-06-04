<?php

namespace Hoo\WordPressPluginFramework\Database;

use Hoo\WordPressPluginFramework\Database\Query\QueryInterface;

interface DatabaseInterface
{
	public function select(QueryInterface $query): array;

	public function startTransaction(): void;
	public function commit(): void;
	public function rollback(): void;
}