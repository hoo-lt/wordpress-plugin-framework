<?php

namespace Hoo\WordPressPluginFramework\Http\Url\Query;

interface QueryFactoryInterface
{
	public function create(array|string $query): QueryInterface;
	public function tryCreate(array|string|null $query): ?QueryInterface;
}
