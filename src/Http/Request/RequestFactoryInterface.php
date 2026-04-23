<?php

namespace Hoo\WordPressPluginFramework\Http\Request;

use Hoo\WordPressPluginFramework\Http\Method\Method;
use Hoo\WordPressPluginFramework\Http\Url\UrlInterface;

interface RequestFactoryInterface
{
	public function from(array $headers, ?string $body, Method $method, UrlInterface $url): RequestInterface;
	public function fromServer(array $server, ?string $input): RequestInterface;
}
