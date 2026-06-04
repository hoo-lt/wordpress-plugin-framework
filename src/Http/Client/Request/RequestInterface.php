<?php

namespace Hoo\WordPressPluginFramework\Http\Client\Request;

use Hoo\WordPressPluginFramework\{
	Http\Message\MessageInterface,
	Http\Method\Method,
	Http\Url\UrlInterface,
};

interface RequestInterface extends MessageInterface
{
	public function method(): Method;
	public function withMethod(Method $method): static;

	public function url(): UrlInterface;
	public function withUrl(UrlInterface $url): static;

	public function queryValues(string $key): ?array;
	public function queryValue(string $key): mixed;

	public function bodyQueryValues(string $key): ?array;
	public function bodyQueryValue(string $key): mixed;
}
