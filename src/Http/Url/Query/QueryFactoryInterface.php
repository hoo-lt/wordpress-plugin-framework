<?php

namespace Hoo\WordPressPluginFramework\Http\Url\Query;

use stdClass;

interface QueryFactoryInterface
{
	public function create(array|stdClass $query): QueryInterface;

	public function createFromEncoded(string $query): QueryInterface;

	public function createFromUnnormalized(mixed $query): QueryInterface;
}
