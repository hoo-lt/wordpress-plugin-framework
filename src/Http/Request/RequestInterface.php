<?php

namespace Hoo\WordPressPluginFramework\Http\Request;

use Hoo\WordPressPluginFramework\{
	Http\Message\MessageInterface,
	Http\Method\Method,
	Http\Url\UrlInterface,
	Http\Request\Routes\RoutesInterface
};

interface RequestInterface extends MessageInterface
{
	public function method(): Method;
	public function withMethod(Method $method): static;

	public function url(): UrlInterface;
	public function withUrl(UrlInterface $url): static;

	public function queryValues(string $key): ?array;
	public function queryValue(string $key): mixed;

	public function routes(): ?RoutesInterface;
	public function withRoutes(RoutesInterface $routes): static;
	public function withoutRoutes(): static;

	public function route(string $key): mixed;

	public function bodyQueryValues(string $key): ?array;
	public function bodyQueryValue(string $key): mixed;
}
