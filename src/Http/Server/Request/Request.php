<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Request;

use Hoo\WordPressPluginFramework\{
	Http\Client\Request\Request as ClientRequest,
	Http\Message\Body\BodyInterface,
	Http\Method\Method,
	Http\Message\Headers\HeadersInterface,
	Http\Url\UrlInterface,
	Http\Server\Request\Routes\RoutesInterface,
};

readonly class Request extends ClientRequest implements RequestInterface
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

	public function withMethod(Method $method): static
	{
		return new static($method, $this->url, $this->headers, $this->body, $this->routes);
	}

	public function withUrl(UrlInterface $url): static
	{
		return new static($this->method, $url, $this->headers, $this->body, $this->routes);
	}

	public function withHeaders(HeadersInterface $headers): static
	{
		return new static($this->method, $this->url, $headers, $this->body, $this->routes);
	}

	public function withoutHeaders(): static
	{
		return new static($this->method, $this->url, null, $this->body, $this->routes);
	}

	public function withBody(BodyInterface $body): static
	{
		return new static($this->method, $this->url, $this->headers, $body, $this->routes);
	}

	public function withoutBody(): static
	{
		return new static($this->method, $this->url, $this->headers, null, $this->routes);
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
