<?php

namespace Hoo\WordPressPluginFramework\Http\Request;

use Hoo\WordPressPluginFramework\{
	Http\Message\MessageInterface,
	Http\Method\Method,
	Http\Url\UrlInterface,
	Uuid\UuidInterface,
};

interface RequestInterface extends MessageInterface
{
	public function uuid(): UuidInterface;

	public function method(): Method;
	public function withMethod(Method $method): static;

	public function url(): UrlInterface;
	public function withUrl(UrlInterface $url): static;

	public function queryValues(string $key): ?array;
	public function queryValue(string $key): mixed;
}
