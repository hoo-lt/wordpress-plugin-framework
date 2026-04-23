<?php

namespace Hoo\WordPressPluginFramework\Http\Response;

use Hoo\WordPressPluginFramework\Http\Headers\HeadersInterface;

readonly class Response implements ResponseInterface
{
	public function __construct(
		protected HeadersInterface $headers,
		protected ?string $body,
		protected int $statusCode,
	) {
		$this->validateStatusCode($statusCode);
	}

	public function headers(): array
	{
		return $this->headers->values();
	}

	public function withHeaders(array $headers): static
	{
		return new static(
			$this->headers->with($headers),
			$this->body,
			$this->statusCode,
		);
	}

	public function withoutHeaders(): static
	{
		return new static(
			$this->headers->without(),
			$this->body,
			$this->statusCode,
		);
	}

	public function header(string $name): ?string
	{
		return $this->headers->value($name);
	}

	public function withHeader(string $name, string $header): static
	{
		return new static(
			$this->headers->withValue($name, $header),
			$this->body,
			$this->statusCode,
		);
	}

	public function withoutHeader(string $name): static
	{
		return new static(
			$this->headers->withoutValue($name),
			$this->body,
			$this->statusCode,
		);
	}

	public function body(): ?string
	{
		return $this->body;
	}

	public function withBody(string $body): static
	{
		return new static(
			$this->headers,
			$body,
			$this->statusCode,
		);
	}

	public function withoutBody(): static
	{
		return new static(
			$this->headers,
			null,
			$this->statusCode,
		);
	}

	public function statusCode(): int
	{
		return $this->statusCode;
	}

	public function withStatusCode(int $statusCode): static
	{
		return new static(
			$this->headers,
			$this->body,
			$statusCode,
		);
	}

	private function validateStatusCode(int $statusCode): void
	{
		if ($statusCode < 100 || $statusCode > 599) {
			throw new ResponseException("Invalid HTTP status code: {$statusCode}");
		}
	}
}
