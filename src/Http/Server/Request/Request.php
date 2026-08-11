<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Request;

use Hoo\WordPressPluginFramework\{
	Http\Message\Body\BodyInterface,
	Http\Method\Method,
	Http\Message\Headers\HeadersInterface,
	Http\KeyValue\KeyValueInterface,
	Http\Url\UrlInterface,
	Uuid\UuidInterface,
};

readonly class Request implements RequestInterface
{
	public function __construct(
		protected UuidInterface $uuid,
		protected Method $method,
		protected UrlInterface $url,
		protected HeadersInterface $headers,
		protected ?BodyInterface $body = null,
	) {
	}

	public function uuid(): UuidInterface
	{
		return $this->uuid;
	}

	public function method(): Method
	{
		return $this->method;
	}

	public function withMethod(Method $method): static
	{
		return new static($this->uuid, $method, $this->url, $this->headers, $this->body);
	}

	public function url(): UrlInterface
	{
		return $this->url;
	}

	public function withUrl(UrlInterface $url): static
	{
		return new static($this->uuid, $this->method, $url, $this->headers, $this->body);
	}

	public function queryValues(string $key): ?array
	{
		$query = $this->url()->query();
		return $query instanceof KeyValueInterface ? $query->values($key) : null;
	}

	public function queryValue(string $key): mixed
	{
		$query = $this->url()->query();
		return $query instanceof KeyValueInterface ? $query->value($key) : null;
	}

	public function headers(): HeadersInterface
	{
		return $this->headers;
	}

	public function withHeaders(HeadersInterface $headers): static
	{
		return new static($this->uuid, $this->method, $this->url, $headers, $this->body);
	}

	public function header(string $name): mixed
	{
		return $this->headers()->header($name);
	}

	public function body(): ?BodyInterface
	{
		return $this->body;
	}

	public function withBody(BodyInterface $body): static
	{
		return new static($this->uuid, $this->method, $this->url, $this->headers, $body);
	}

	public function withoutBody(): static
	{
		return new static($this->uuid, $this->method, $this->url, $this->headers, null);
	}

	public function bodyValues(string $key): ?array
	{
		$body = $this->body();
		return $body instanceof KeyValueInterface ? $body->values($key) : null;
	}

	public function bodyValue(string $key): mixed
	{
		$body = $this->body();
		return $body instanceof KeyValueInterface ? $body->value($key) : null;
	}
}
