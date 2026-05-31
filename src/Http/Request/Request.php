<?php

namespace Hoo\WordPressPluginFramework\Http\Request;

use Hoo\WordPressPluginFramework\{
	Http\Body\BodyInterface,
	Http\Method\Method,
	Http\Headers\HeadersInterface,
	Http\KeyValue\KeyValueInterface,
	Http\Url\UrlInterface,
	Http\Request\Routes\RoutesInterface,
};

readonly class Request implements RequestInterface
{
	public function __construct(
		protected Method $method,
		protected UrlInterface $url,
		protected ?HeadersInterface $headers = null,
		protected ?BodyInterface $body = null,
		protected ?RoutesInterface $routes = null,
	) {
	}

	public function method(): Method
	{
		return $this->method;
	}

	public function withMethod(Method $method): static
	{
		return new static($method, $this->url, $this->headers, $this->body, $this->routes);
	}

	public function url(): UrlInterface
	{
		return $this->url;
	}

	public function withUrl(UrlInterface $url): static
	{
		return new static($this->method, $url, $this->headers, $this->body, $this->routes);
	}

	public function urlQueryValues(string $key): array
	{
		$query = $this->url()->query();
		return $query instanceof KeyValueInterface ? $query->values($key) : [];
	}

	public function urlQueryValue(string $key): mixed
	{
		$query = $this->url()->query();
		return $query instanceof KeyValueInterface ? $query->value($key) : null;
	}

	public function headers(): ?HeadersInterface
	{
		return $this->headers;
	}

	public function withHeaders(HeadersInterface $headers): static
	{
		return new static($this->method, $this->url, $headers, $this->body, $this->routes);
	}

	public function withoutHeaders(): static
	{
		return new static($this->method, $this->url, null, $this->body, $this->routes);
	}

	public function header(string $key): mixed
	{
		return $this->headers()?->header($key);
	}

	public function body(): ?BodyInterface
	{
		return $this->body;
	}

	public function withBody(BodyInterface $body): static
	{
		return new static($this->method, $this->url, $this->headers, $body, $this->routes);
	}

	public function withoutBody(): static
	{
		return new static($this->method, $this->url, $this->headers, null, $this->routes);
	}

	public function bodyValues(string $key): array
	{
		$body = $this->body();
		return $body instanceof KeyValueInterface ? $body->values($key) : [];
	}

	public function bodyValue(string $key): mixed
	{
		$body = $this->body();
		return $body instanceof KeyValueInterface ? $body->value($key) : null;
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

	public function values(string $key): array
	{
		$bodyValues = $this->bodyValues($key);
		if ($bodyValues !== []) {
			return $bodyValues;
		}

		$urlQueryValues = $this->urlQueryValues($key);
		if ($urlQueryValues !== []) {
			return $urlQueryValues;
		}

		return [];
	}

	public function value(string $key): mixed
	{
		$bodyValue = $this->bodyValue($key);
		if ($bodyValue !== null) {
			return $bodyValue;
		}

		$urlQueryValue = $this->urlQueryValue($key);
		if ($urlQueryValue !== null) {
			return $urlQueryValue;
		}

		return null;
	}
}
