<?php

namespace Hoo\WordPressPluginFramework\Database\Select;

use Hoo\WordPressPluginFramework\Database\Queries;

interface SelectInterface
{
	public function __invoke(Queries\Select\QueryInterface $query): array;
}