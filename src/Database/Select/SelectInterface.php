<?php

namespace Hoo\WordPressPluginFramework\Database;

interface SelectInterface
{
	public function __invoke(Queries\Select\QueryInterface $query): array;
}