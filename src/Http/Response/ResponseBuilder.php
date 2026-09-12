<?php

namespace Hoo\WordPressPluginFramework\Http\Response;

use Hoo\WordPressPluginFramework\{
	Http\Message\Body\BodyFactoryInterface,
	Http\Message\Body\BodyInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
	Http\Message\Headers\HeadersFactoryInterface,
	Http\Message\Headers\HeadersInterface,
	Uuid\UuidInterface,
};

readonly class ResponseBuilder implements ResponseBuilderInterface
{
	protected HeadersInterface $headers;

	public function __construct(
		protected HeadersFactoryInterface $headersFactory,
		protected BodyFactoryInterface $bodyFactory,
		protected UuidInterface $uuid,
		protected ?int $statusCode = null,
		?HeadersInterface $headers = null,
		protected ?BodyInterface $body = null,
	) {
		$this->headers = $headers ?? $headersFactory->create();
	}

	public function withStatusCode(int $statusCode): static
	{
		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $statusCode, $this->headers, $this->body);
	}

	public function withHeaders(HeadersInterface|array $headers): static
	{
		if (!$headers instanceof HeadersInterface) {
			$headers = $this->headersFactory->create($headers);
		}

		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $this->statusCode, $headers, $this->body);
	}

	public function withBody(mixed $body): static
	{
		if (!$body instanceof BodyInterface) {
			$body = $this->bodyFactory->createBody($this->contentType(), $body);
		}

		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $this->statusCode, $this->headers, $body);
	}

	public function withUnnormalizedBody(mixed $body): static
	{
		$body = $this->bodyFactory->createBodyFromUnnormalized($this->contentType(), $body);

		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $this->statusCode, $this->headers, $body);
	}

	public function withoutBody(): static
	{
		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $this->statusCode, $this->headers, null);
	}

	protected function contentType(): MediaTypeInterface
	{
		return $this->headers->contentType()
			?? throw new ResponseBuilderException('content type is mandatory to encode a body');
	}

	public function build(): ResponseInterface
	{
		if ($this->statusCode === null) {
			throw new ResponseBuilderException('status code is mandatory');
		}

		return new Response($this->uuid, $this->statusCode, $this->headers, $this->body);
	}
}
