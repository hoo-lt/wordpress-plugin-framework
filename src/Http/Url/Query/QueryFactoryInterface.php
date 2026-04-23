<?php

namespace Hoo\WordPressPluginFramework\Http\Url\Query;

interface QueryFactoryInterface
{
	public function from(array $query): QueryInterface;
	public function fromQuery(string $query): QueryInterface;
}
