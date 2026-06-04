<?php

namespace Hoo\WordPressPluginFramework\Database\Select;

use Hoo\WordPressPluginFramework\Database\Query\QueryInterface;

interface SelectInterface
{
	public function __invoke(QueryInterface $query): array;
}