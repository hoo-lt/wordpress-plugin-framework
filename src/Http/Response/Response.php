<?php

namespace Hoo\WordPressPluginFramework\Http\Response;

use Hoo\WordPressPluginFramework\{
	Http\Message\Body\BodyInterface,
	Http\Message\Headers\HeadersInterface,
	Http\KeyValue\KeyValueInterface,
	Uuid\UuidInterface,
};

readonly class Response implements ResponseInterface
{
	public function __construct(
		protected UuidInterface $uuid,
		protected int $statusCode,
		protected HeadersInterface $headers,
		protected ?BodyInterface $body = null,
	) {
		$this->validateStatusCode($statusCode);
	}

	public function uuid(): UuidInterface
	{
		return $this->uuid;
	}

	public function statusCode(): int
	{
		return $this->statusCode;
	}

	public function withStatusCode(int $statusCode): static
	{
		return new static($this->uuid, $statusCode, $this->headers, $this->body);
	}

	public function headers(): HeadersInterface
	{
		return $this->headers;
	}

	public function withHeaders(HeadersInterface $headers): static //maybe Closure for better DX?
	{
		return new static($this->uuid, $this->statusCode, $headers, $this->body);
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
		return new static($this->uuid, $this->statusCode, $this->headers, $body);
	}

	public function withoutBody(): static
	{
		return new static($this->uuid, $this->statusCode, $this->headers, null);
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

	protected function validateStatusCode(int $statusCode): void
	{
		if (
			$statusCode < 100 ||
			$statusCode > 599
		) {
			throw new ResponseException("Invalid HTTP status code: {$statusCode}");
		}
	}
}
