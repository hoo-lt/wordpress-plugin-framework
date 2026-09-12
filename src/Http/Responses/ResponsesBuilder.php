<?php

namespace Hoo\WordPressPluginFramework\Http\Responses;

use Hoo\WordPressPluginFramework\{
	Http\Message\Body\BodyFactoryInterface,
	Http\Message\Headers\HeadersFactoryInterface,
	Http\Message\Headers\HeadersInterface,
	Http\Response\Response,
	Uuid\UuidInterface,
};

readonly class ResponsesBuilder implements ResponsesBuilderInterface
{
	protected HeadersInterface $headers;

	public function __construct(
		protected HeadersFactoryInterface $headersFactory,
		protected BodyFactoryInterface $bodyFactory,
		protected UuidInterface $uuid,
		protected ?int $statusCode = null,
		?HeadersInterface $headers = null,
		protected array $bodies = [],
	) {
		$this->headers = $headers ?? $headersFactory->create();
	}

	public function withStatusCode(int $statusCode): static
	{
		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $statusCode, $this->headers, $this->bodies);
	}

	public function withHeaders(HeadersInterface|array $headers): static
	{
		if (!$headers instanceof HeadersInterface) {
			$headers = $this->headersFactory->create($headers);
		}

		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $this->statusCode, $headers, $this->bodies);
	}

	public function withBodies(mixed $body): static
	{
		$bodies = [
			...$this->bodies,
			...$this->bodyFactory->createBodies($body),
		];

		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $this->statusCode, $this->headers, $bodies);
	}

	public function withUnnormalizedBodies(mixed $body): static
	{
		$bodies = [
			...$this->bodies,
			...$this->bodyFactory->createBodiesFromUnnormalized($body),
		];

		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $this->statusCode, $this->headers, $bodies);
	}

	public function withoutBodies(): static
	{
		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $this->statusCode, $this->headers, []);
	}

	public function build(): ResponsesInterface
	{
		if ($this->statusCode === null) {
			throw new ResponsesBuilderException('status code is mandatory');
		}

		if ($this->bodies === []) {
			throw new ResponsesBuilderException('building representations without bodies is prohibited');
		}

		$responses = new Responses();

		foreach ($this->bodies as $body) {
			$response = new Response($this->uuid, $this->statusCode, $this->headers, $body);

			$responses = $responses->with($response);
		}

		return $responses;
	}
}
