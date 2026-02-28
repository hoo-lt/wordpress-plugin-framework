<?php

namespace Hoo\WordpressPluginFramework\Database;

interface DatabaseInterface
{
	public function select(Query\Select\QueryInterface $query): ?array;
}