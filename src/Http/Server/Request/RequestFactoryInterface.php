<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Request;

interface RequestFactoryInterface
{
	public function create(string $method, string $url, array $headers = [], ?string $body = null, ?array $routes = null): RequestInterface;
	public function createFromServer(): RequestInterface;
}
