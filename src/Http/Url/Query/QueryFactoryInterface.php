<?php

namespace Hoo\WordPressPluginFramework\Http\Url\Query;

interface QueryFactoryInterface
{
	public function from(array|string $query): QueryInterface;
	public function tryFrom(array|string|null $query): ?QueryInterface;
}
