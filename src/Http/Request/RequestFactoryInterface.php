<?php

namespace Hoo\WordPressPluginFramework\Http\Request;

interface RequestFactoryInterface
{
	public function from(string $method, string $url, ?array $headers = null, array|string|null $body = null): RequestInterface;

	public function fromServer(): RequestInterface;
}
