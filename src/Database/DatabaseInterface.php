<?php

namespace Hoo\WordPressPluginFramework\Database;

interface DatabaseInterface
{
	public function select(Query\Select\QueryInterface $query): array;
	public function json(Query\Select\QueryInterface $query): array;
}