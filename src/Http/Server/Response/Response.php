<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Response;

use Hoo\WordPressPluginFramework\{
	Http\Client\Response\Response as ClientResponse,
	Http\Message\Body\BodyInterface,
	Http\Message\Headers\HeadersInterface,
	Uuid\UuidInterface,
};

readonly class Response extends ClientResponse implements ResponseInterface
{
	public function __construct(
		protected UuidInterface $uuid,
		int $statusCode,
		HeadersInterface $headers,
		?BodyInterface $body,
	) {
		parent::__construct($statusCode, $headers, $body);
	}

	public function uuid(): UuidInterface
	{
		return $this->uuid;
	}

	public function withStatusCode(int $statusCode): static
	{
		return new static($this->uuid, $statusCode, $this->headers, $this->body);
	}

	public function withHeaders(HeadersInterface $headers): static
	{
		return new static($this->uuid, $this->statusCode, $headers, $this->body);
	}

	public function withBody(BodyInterface $body): static
	{
		return new static($this->uuid, $this->statusCode, $this->headers, $body);
	}

	public function withoutBody(): static
	{
		return new static($this->uuid, $this->statusCode, $this->headers, null);
	}
}
