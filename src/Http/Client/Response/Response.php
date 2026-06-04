<?php

namespace Hoo\WordPressPluginFramework\Http\Response;

use Hoo\WordPressPluginFramework\{
	Http\Body\BodyInterface,
	Http\Headers\HeadersInterface,
	Http\KeyValue\KeyValueInterface,
};

readonly class Response implements ResponseInterface
{
	public function __construct(
		protected int $statusCode,
		protected ?HeadersInterface $headers,
		protected ?BodyInterface $body,
	) {
		$this->validateStatusCode($statusCode);
	}

	public function statusCode(): int
	{
		return $this->statusCode;
	}

	public function withStatusCode(int $statusCode): static
	{
		return new static(
			$statusCode,
			$this->headers(),
			$this->body,
		);
	}

	public function headers(): ?HeadersInterface
	{
		return $this->headers;
	}

	public function withHeaders(HeadersInterface $headers): static
	{
		return new static(
			$this->statusCode,
			$headers,
			$this->body,
		);
	}

	public function withoutHeaders(): static
	{
		return new static(
			$this->statusCode,
			null,
			$this->body,
		);
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
		return new static(
			$this->statusCode,
			$this->headers,
			$body,
		);
	}

	public function withoutBody(): static
	{
		return new static(
			$this->statusCode,
			$this->headers,
			null,
		);
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
