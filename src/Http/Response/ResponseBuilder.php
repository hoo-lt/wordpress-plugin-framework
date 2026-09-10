<?php

namespace Hoo\WordPressPluginFramework\Http\Response;

use Hoo\WordPressPluginFramework\{
	Http\Message\Body\BodyFactoryInterface,
	Http\Message\Body\BodyInterface,
	Http\Message\Headers\HeadersFactoryInterface,
	Http\Response\Response,
	Uuid\UuidInterface,
};

readonly class ResponseBuilder implements ResponseBuilderInterface
{
	public function __construct(
		protected HeadersFactoryInterface $headersFactory,
		protected BodyFactoryInterface $bodyFactory,
		protected UuidInterface $uuid,
		protected ?int $statusCode = null,
		protected array $headers = [],
		protected ?string $contentType = null,
		protected ?BodyInterface $body = null,
	) {
	}

	public function withStatusCode(int $statusCode): static
	{
		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $statusCode, $this->headers, $this->contentType, $this->body);
	}

	public function withHeaders(array $headers): static
	{
		$headers = array_change_key_case($headers, CASE_LOWER);

		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $this->statusCode, $headers, $this->contentType, $this->body);
	}

	public function withHeader(string $name, string $value): static
	{
		$headers = $this->headers;
		$headers[strtolower($name)] = $value;

		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $this->statusCode, $headers, $this->contentType, $this->body);
	}

	public function withoutHeader(string $name): static
	{
		$headers = $this->headers;
		unset($headers[strtolower($name)]);

		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $this->statusCode, $headers, $this->contentType, $this->body);
	}

	public function withBody(string $contentType, mixed $body): static
	{
		$body = $this->bodyFactory->createBody($contentType, $body);

		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $this->statusCode, $this->headers, $contentType, $body);
	}

	public function withUnnormalizedBody(string $contentType, mixed $body): static
	{
		$body = $this->bodyFactory->createBodyFromUnnormalized($contentType, $body);

		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $this->statusCode, $this->headers, $contentType, $body);
	}

	public function withoutBody(): static
	{
		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $this->statusCode, $this->headers, null, null);
	}

	public function build(): ResponseInterface
	{
		if ($this->statusCode === null) {
			throw new ResponseBuilderException('status code is mandatory');
		}

		$headers = $this->headers;

		if ($this->body === null) {
			unset($headers['content-type']);
		} else {
			$headers['content-type'] = $this->contentType;
		}

		$headers = $this->headersFactory->create($headers);

		return new Response($this->uuid, $this->statusCode, $headers, $this->body);
	}
}
