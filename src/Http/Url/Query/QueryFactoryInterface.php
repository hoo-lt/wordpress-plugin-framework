<?php

namespace Hoo\WordPressPluginFramework\Http\Url\Query;

interface QueryFactoryInterface
{
	public function from(string $query): QueryInterface;
	public function fromServer(): ?QueryInterface;
}
