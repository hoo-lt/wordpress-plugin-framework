<?php

namespace Hoo\WordPressPluginFramework\Http\Request;

interface RequestFactoryInterface
{
	public function from(string $method, string $url, ?array $headers = null, ?string $body = null): RequestInterface;
	public function fromServer(): RequestInterface;
}
