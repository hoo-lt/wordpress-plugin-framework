<?php

namespace Hoo\WordPressPluginFramework\Http\Client\Response;

use Hoo\WordPressPluginFramework\{
	Http\Message\Body\BodyFactoryInterface,
	Http\Message\Headers\HeadersFactoryInterface,
	Value\Value as Body,
};

readonly class ResponseBuilder implements ResponseBuilderInterface
{
	protected array $headers;

	protected const HEADERS = [
		'content-type' => 'application/octet-stream'
	];

	public function __construct(
		protected BodyFactoryInterface $bodyFactory,
		protected HeadersFactoryInterface $headersFactory,
		protected int $statusCode = 0,
		array $headers = [],
		protected ?Body $body = null,
	) {
		$this->headers = $this->normalizeHeaders($headers);
	}

	public function withStatusCode(int $statusCode): static
	{
		return new static($this->bodyFactory, $this->headersFactory, $statusCode, $this->headers, $this->body);
	}

	public function withHeader(string $name, string $value): static
	{
		$headers = $this->headers;
		$headers[strtolower($name)] = $value;

		return new static($this->bodyFactory, $this->headersFactory, $this->statusCode, $headers, $this->body);
	}

	public function withoutHeader(string $name): static
	{
		$headers = $this->headers;
		unset($headers[strtolower($name)]);

		return new static($this->bodyFactory, $this->headersFactory, $this->statusCode, $headers, $this->body);
	}

	public function withBody(mixed $body): static
	{
		$body = new Body($body);

		return new static($this->bodyFactory, $this->headersFactory, $this->statusCode, $this->headers, $body);
	}

	public function withoutBody(): static
	{
		return new static($this->bodyFactory, $this->headersFactory, $this->statusCode, $this->headers, null);
	}

	public function build(): ResponseInterface
	{
		$headers = $this->body === null ? $this->headers : [
			...static::HEADERS,
			...$this->headers,
		];

		return new Response(
			$this->statusCode,
			$this->headersFactory->create($headers),
			$this->body === null ? null : $this->bodyFactory->createFromDecoded($this->body->value, $headers['content-type']),
		);
	}

	protected function normalizeHeaders(array $headers): array
	{
		return array_change_key_case($headers, CASE_LOWER);
	}
}
