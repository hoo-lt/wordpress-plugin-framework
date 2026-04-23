<?php

namespace Hoo\WordPressPluginFramework\Http\Request;

use Hoo\WordPressPluginFramework\Http\Method\Method;
use Hoo\WordPressPluginFramework\Http\Request\Body\BodyInterface;
use Hoo\WordPressPluginFramework\Http\Url\UrlInterface;

interface RequestFactoryInterface
{
	public function from(array $headers, ?BodyInterface $body, Method $method, UrlInterface $url): RequestInterface;
	public function fromServer(array $server, ?string $body): RequestInterface;
}
