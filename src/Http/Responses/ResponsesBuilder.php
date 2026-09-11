<?php

namespace Hoo\WordPressPluginFramework\Http\Responses;

use Hoo\WordPressPluginFramework\{
	Http\Message\Body\BodyFactoryInterface,
	Http\Message\Headers\HeadersFactoryInterface,
	Http\Response\Response,
	Uuid\UuidInterface,
};

readonly class ResponsesBuilder implements ResponsesBuilderInterface
{
	public function __construct(
		protected HeadersFactoryInterface $headersFactory,
		protected BodyFactoryInterface $bodyFactory,
		protected UuidInterface $uuid,
		protected ?int $statusCode = null,
		protected array $headers = [],
		protected array $bodies = [],
	) {
	}

	public function withStatusCode(int $statusCode): static
	{
		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $statusCode, $this->headers, $this->bodies);
	}

	public function withHeaders(array $headers): static
	{
		$headers = array_change_key_case($headers, CASE_LOWER);

		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $this->statusCode, $headers, $this->bodies);
	}

	public function withoutHeaders(): static
	{
		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $this->statusCode, [], $this->bodies);
	}

	public function withHeader(string $name, string $value): static
	{
		$headers = $this->headers;
		$headers[strtolower($name)] = $value;

		return new static($this->headersFactory, $this->bodyFactory, $this->uuid, $this->statusCode, $headers, $this->bodies);
	}

	public function withoutHeader(string $name): static
	{
		$headers = $this->headers;
		unset($headers[strtolower($name)]);

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

		$headers = $this->headersFactory->create($this->headers);

		$responses = new Responses();

		foreach ($this->bodies as $body) {
			$response = new Response($this->uuid, $this->statusCode, $headers, $body);

			$responses = $responses->with($response);
		}

		return $responses;
	}
}
