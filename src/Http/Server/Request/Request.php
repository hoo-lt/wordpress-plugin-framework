<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Request;

use Hoo\WordPressPluginFramework\{
	Http\Client,
	Http\Message\Body\BodyInterface,
	Http\Method\Method,
	Http\Message\Headers\HeadersInterface,
	Http\Url\UrlInterface,
	Http\Server\Request\Routes\RoutesInterface,
};

readonly class Request extends Client\Request\Request implements RequestInterface
{
	public function __construct(
		Method $method,
		UrlInterface $url,
		?HeadersInterface $headers = null,
		?BodyInterface $body = null,
		protected ?RoutesInterface $routes = null,
	) {
		parent::__construct($method, $url, $headers, $body);
	}

	public function routes(): ?RoutesInterface
	{
		return $this->routes;
	}

	public function withRoutes(RoutesInterface $routes): static
	{
		return new static($this->method, $this->url, $this->headers, $this->body, $routes);
	}

	public function withoutRoutes(): static
	{
		return new static($this->method, $this->url, $this->headers, $this->body, null);
	}

	public function route(string $key): mixed
	{
		return $this->routes()?->route($key);
	}
}
