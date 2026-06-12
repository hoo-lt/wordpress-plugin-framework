<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Response;

use Hoo\WordPressPluginFramework\{
	Http\Client\Response\Response as ClientResponse,
	Http\Message\Body\BodyInterface,
	Http\Message\Headers\HeadersInterface,
};

readonly class Response extends ClientResponse implements ResponseInterface
{
	public function __construct(
		protected int $statusCode,
		protected ?HeadersInterface $headers,
		protected ?BodyInterface $body,
	) {
		parent::__construct($statusCode, $headers, $body);
	}

	public function withStatusCode(int $statusCode): static
	{
		return new static($statusCode, $this->headers, $this->body);
	}

	public function withHeaders(HeadersInterface $headers): static
	{
		return new static($this->statusCode, $headers, $this->body);
	}

	public function withoutHeaders(): static
	{
		return new static($this->statusCode, null, $this->body);
	}

	public function withBody(BodyInterface $body): static
	{
		return new static($this->statusCode, $this->headers, $body);
	}

	public function withoutBody(): static
	{
		return new static($this->statusCode, $this->headers, null);
	}
}
