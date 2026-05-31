<?php

namespace Hoo\WordPressPluginFramework\Http\Request;

use Hoo\WordPressPluginFramework\Http;

readonly class Request implements RequestInterface
{
	public function __construct(
		protected Http\Method\Method $method,
		protected Http\Url\UrlInterface $url,
		protected ?Http\Headers\HeadersInterface $headers = null,
		protected ?Http\Body\BodyInterface $body = null,
		protected ?Routes\RoutesInterface $routes = null,
	) {
	}

	public function method(): Http\Method\Method
	{
		return $this->method;
	}

	public function withMethod(Http\Method\Method $method): static
	{
		return new static(
			$method,
			$this->url,
			$this->headers,
			$this->body,
			$this->routes
		);
	}

	public function url(): Http\Url\UrlInterface
	{
		return $this->url;
	}

	public function withUrl(Http\Url\UrlInterface $url): static
	{
		return new static(
			$this->method,
			$url,
			$this->headers,
			$this->body,
			$this->routes
		);
	}

	public function headers(): ?Http\Headers\HeadersInterface
	{
		return $this->headers;
	}

	public function withHeaders(Http\Headers\HeadersInterface $headers): static
	{
		return new static(
			$this->method,
			$this->url,
			$headers,
			$this->body,
			$this->routes
		);
	}

	public function withoutHeaders(): static
	{
		return new static(
			$this->method,
			$this->url,
			null,
			$this->body,
			$this->routes
		);
	}

	public function body(): ?Http\Body\BodyInterface
	{
		return $this->body;
	}

	public function withBody(Http\Body\BodyInterface $body): static
	{
		return new static(
			$this->method,
			$this->url,
			$this->headers,
			$body,
			$this->routes
		);
	}

	public function withoutBody(): static
	{
		return new static(
			$this->method,
			$this->url,
			$this->headers,
			null,
			$this->routes
		);
	}

	public function routes(): ?Routes\RoutesInterface
	{
		return $this->routes;
	}

	public function withRoutes(Routes\RoutesInterface $routes): static
	{
		return new static(
			$this->method,
			$this->url,
			$this->headers,
			$this->body,
			$routes,
		);
	}

	public function withoutRoutes(): static
	{
		return new static(
			$this->method,
			$this->url,
			$this->headers,
			$this->body,
			null,
		);
	}
}
