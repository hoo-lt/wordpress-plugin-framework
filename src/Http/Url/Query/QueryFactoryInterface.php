<?php

namespace Hoo\WordPressPluginFramework\Http\Url\Query;

interface QueryFactoryInterface
{
	public function create(array $query): QueryInterface;
	public function createFromEncoded(string $query): QueryInterface;
}
