<?php

namespace Hoo\WordPressPluginFramework\Http\Response;

use Hoo\WordPressPluginFramework\Http;

readonly class Response implements ResponseInterface
{
	public function __construct(
		protected int $statusCode,
		protected ?Http\Headers\HeadersInterface $headers,
		protected ?Http\Body\BodyInterface $body,
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

	public function headers(): ?Http\Headers\HeadersInterface
	{
		return $this->headers;
	}

	public function withHeaders(Http\Headers\HeadersInterface $headers): static
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

	public function body(): ?Http\Body\BodyInterface
	{
		return $this->body;
	}

	public function withBody(Http\Body\BodyInterface $body): static
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
