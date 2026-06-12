<?php

namespace Hoo\WordPressPluginFramework\Http\Client\Request;

interface RequestFactoryInterface
{
	public function create(string $method, string $url, ?array $headers = null, array|string|null $body = null): RequestInterface;
}
