<?php

namespace Hoo\WordPressPluginFramework\Http\Request;

use Hoo\WordPressPluginFramework\Http;

interface RequestInterface extends Http\Message\MessageInterface
{
	public function method(): Http\Method\Method;
	public function withMethod(Http\Method\Method $method): static;

	public function url(): Http\Url\UrlInterface;
	public function withUrl(Http\Url\UrlInterface $url): static;


	public function routes(): ?Routes\RoutesInterface;
	public function withRoutes(Routes\RoutesInterface $routes): static;
	public function withoutRoutes(): static;
}
