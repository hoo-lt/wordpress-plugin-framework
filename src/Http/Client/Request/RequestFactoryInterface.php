<?php

namespace Hoo\WordPressPluginFramework\Http\Client\Request;

interface RequestFactoryInterface
{
	public function create(string $method, string $url, ?array $headers = null, mixed $body = null): RequestInterface;
}
