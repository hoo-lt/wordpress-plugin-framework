<?php

namespace Hoo\WordPressPluginFramework\Http\Request;

use Hoo\WordPressPluginFramework\{
	Http\Message\Body\BodyFactoryInterface,
	Http\Message\Body\BodyInterface,
	Http\Message\Headers\HeadersFactoryInterface,
	Http\Method\Method,
	Http\Url\UrlFactoryInterface,
	Http\Url\UrlInterface,
	Uuid\UuidInterface,
};

readonly class RequestBuilder implements RequestBuilderInterface
{
	public function __construct(
		protected UrlFactoryInterface $urlFactory,
		protected HeadersFactoryInterface $headersFactory,
		protected BodyFactoryInterface $bodyFactory,
		protected UuidInterface $uuid,
		protected ?Method $method = null,
		protected ?UrlInterface $url = null,
		protected array $headers = [],
		protected ?string $contentType = null,
		protected ?BodyInterface $body = null,
	) {
	}

	public function withMethod(string $method): static
	{
		$method = Method::create($method);

		return new static($this->urlFactory, $this->headersFactory, $this->bodyFactory, $this->uuid, $method, $this->url, $this->headers, $this->contentType, $this->body);
	}

	public function withUrl(string $url): static
	{
		$url = $this->urlFactory->create($url);

		return new static($this->urlFactory, $this->headersFactory, $this->bodyFactory, $this->uuid, $this->method, $url, $this->headers, $this->contentType, $this->body);
	}

	public function withHeaders(array $headers): static
	{
		$headers = array_change_key_case($headers, CASE_LOWER);

		return new static($this->urlFactory, $this->headersFactory, $this->bodyFactory, $this->uuid, $this->method, $this->url, $headers, $this->contentType, $this->body);
	}

	public function withHeader(string $name, string $value): static
	{
		$headers = $this->headers;
		$headers[strtolower($name)] = $value;

		return new static($this->urlFactory, $this->headersFactory, $this->bodyFactory, $this->uuid, $this->method, $this->url, $headers, $this->contentType, $this->body);
	}

	public function withoutHeader(string $name): static
	{
		$headers = $this->headers;
		unset($headers[strtolower($name)]);

		return new static($this->urlFactory, $this->headersFactory, $this->bodyFactory, $this->uuid, $this->method, $this->url, $headers, $this->contentType, $this->body);
	}

	public function withBody(string $contentType, mixed $body): static
	{
		$body = $this->bodyFactory->createBody($contentType, $body);

		return new static($this->urlFactory, $this->headersFactory, $this->bodyFactory, $this->uuid, $this->method, $this->url, $this->headers, $contentType, $body);
	}

	public function withUnnormalizedBody(string $contentType, mixed $body): static
	{
		$body = $this->bodyFactory->createBodyFromUnnormalized($contentType, $body);

		return new static($this->urlFactory, $this->headersFactory, $this->bodyFactory, $this->uuid, $this->method, $this->url, $this->headers, $contentType, $body);
	}

	public function withoutBody(): static
	{
		return new static($this->urlFactory, $this->headersFactory, $this->bodyFactory, $this->uuid, $this->method, $this->url, $this->headers, null, null);
	}

	public function build(): RequestInterface
	{
		if ($this->method === null) {
			throw new RequestBuilderException('method is mandatory');
		}

		if ($this->url === null) {
			throw new RequestBuilderException('url is mandatory');
		}

		$headers = $this->headers;

		if ($this->body === null) {
			unset($headers['content-type']);
		} else {
			$headers['content-type'] = $this->contentType;
		}

		$headers = $this->headersFactory->create($headers);

		return new Request($this->uuid, $this->method, $this->url, $headers, $this->body);
	}
}
