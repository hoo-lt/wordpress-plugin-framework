<?php

namespace Hoo\WordPressPluginFramework\Http\Client\Request;

interface RequestFactoryInterface
{
	public function create(string $method, string $url, array $headers = [], object|array|string|float|int|bool|null $body = null): RequestInterface;
}
