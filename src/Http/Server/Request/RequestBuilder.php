<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Request;

use Hoo\WordPressPluginFramework\{
	Http\Message\Body\BodyFactoryInterface,
	Http\Message\Headers\HeadersFactoryInterface,
	Http\Method\Method,
	Http\Url\UrlFactoryInterface,
	Uuid\UuidInterface,
	Value\Value as Body,
};

readonly class RequestBuilder implements RequestBuilderInterface
{
	protected array $headers;

	protected const HEADERS = [
		'content-type' => 'application/octet-stream'
	];

	public function __construct(
		protected BodyFactoryInterface $bodyFactory,
		protected HeadersFactoryInterface $headersFactory,
		protected UrlFactoryInterface $urlFactory,
		protected UuidInterface $uuid,
		protected string $method = '',
		protected string $url = '',
		array $headers = [],
		protected ?Body $body = null,
	) {
		$this->headers = $this->normalizeHeaders($headers);
	}

	public function withMethod(string $method): static
	{
		return new static($this->bodyFactory, $this->headersFactory, $this->urlFactory, $this->uuid, $method, $this->url, $this->headers, $this->body);
	}

	public function withUrl(string $url): static
	{
		return new static($this->bodyFactory, $this->headersFactory, $this->urlFactory, $this->uuid, $this->method, $url, $this->headers, $this->body);
	}

	public function withHeader(string $name, string $value): static
	{
		$headers = $this->headers;
		$headers[strtolower($name)] = $value;

		return new static($this->bodyFactory, $this->headersFactory, $this->urlFactory, $this->uuid, $this->method, $this->url, $headers, $this->body);
	}

	public function withoutHeader(string $name): static
	{
		$headers = $this->headers;
		unset($headers[strtolower($name)]);

		return new static($this->bodyFactory, $this->headersFactory, $this->urlFactory, $this->uuid, $this->method, $this->url, $headers, $this->body);
	}

	public function withBody(mixed $body): static
	{
		$body = new Body($body);

		return new static($this->bodyFactory, $this->headersFactory, $this->urlFactory, $this->uuid, $this->method, $this->url, $this->headers, $body);
	}

	public function withoutBody(): static
	{
		return new static($this->bodyFactory, $this->headersFactory, $this->urlFactory, $this->uuid, $this->method, $this->url, $this->headers, null);
	}

	public function build(): RequestInterface
	{
		$headers = $this->body === null ? $this->headers : [
			...static::HEADERS,
			...$this->headers,
		];

		return new Request(
			$this->uuid,
			Method::create($this->method),
			$this->urlFactory->create($this->url),
			$this->headersFactory->create($headers),
			$this->body === null ? null : $this->bodyFactory->createFromDecoded($this->body->value, $headers['content-type']),
		);
	}

	protected function normalizeHeaders(array $headers): array
	{
		return array_change_key_case($headers, CASE_LOWER);
	}
}
