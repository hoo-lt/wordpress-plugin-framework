<?php

namespace Hoo\WordPressPluginFramework\Http\Request;

use Hoo\WordPressPluginFramework\{
	Http\Message\Body\BodyFactoryInterface,
	Http\Message\Body\BodyInterface,
	Http\Message\Headers\HeadersFactoryInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
	Http\Message\Headers\HeadersInterface,
	Http\Method\Method,
	Http\Url\UrlFactoryInterface,
	Http\Url\UrlInterface,
	Uuid\UuidInterface,
};

readonly class RequestBuilder implements RequestBuilderInterface
{
	protected HeadersInterface $headers;

	public function __construct(
		protected UrlFactoryInterface $urlFactory,
		protected HeadersFactoryInterface $headersFactory,
		protected BodyFactoryInterface $bodyFactory,
		protected UuidInterface $uuid,
		protected ?Method $method = null,
		protected ?UrlInterface $url = null,
		?HeadersInterface $headers = null,
		protected ?BodyInterface $body = null,
	) {
		$this->headers = $headers ?? $headersFactory->create();
	}

	public function withMethod(Method|string $method): static
	{
		if (!$method instanceof Method) {
			$method = Method::create($method);
		}

		return new static($this->urlFactory, $this->headersFactory, $this->bodyFactory, $this->uuid, $method, $this->url, $this->headers, $this->body);
	}

	public function withUrl(UrlInterface|string $url): static
	{
		if (!$url instanceof UrlInterface) {
			$url = $this->urlFactory->create($url);
		}

		return new static($this->urlFactory, $this->headersFactory, $this->bodyFactory, $this->uuid, $this->method, $url, $this->headers, $this->body);
	}

	public function withHeaders(HeadersInterface|array $headers): static
	{
		if (!$headers instanceof HeadersInterface) {
			$headers = $this->headersFactory->create($headers);
		}

		return new static($this->urlFactory, $this->headersFactory, $this->bodyFactory, $this->uuid, $this->method, $this->url, $headers, $this->body);
	}

	public function withBody(mixed $body): static
	{
		if (!$body instanceof BodyInterface) {
			$body = $this->bodyFactory->createBody($this->contentType(), $body);
		}

		return new static($this->urlFactory, $this->headersFactory, $this->bodyFactory, $this->uuid, $this->method, $this->url, $this->headers, $body);
	}

	public function withUnnormalizedBody(mixed $body): static
	{
		$body = $this->bodyFactory->createBodyFromUnnormalized($this->contentType(), $body);

		return new static($this->urlFactory, $this->headersFactory, $this->bodyFactory, $this->uuid, $this->method, $this->url, $this->headers, $body);
	}

	public function withoutBody(): static
	{
		return new static($this->urlFactory, $this->headersFactory, $this->bodyFactory, $this->uuid, $this->method, $this->url, $this->headers, null);
	}

	protected function contentType(): MediaTypeInterface
	{
		return $this->headers->contentType()
			?? throw new RequestBuilderException('content type is mandatory to encode a body');
	}

	public function build(): RequestInterface
	{
		if (!$this->method instanceof Method) {
			throw new RequestBuilderException('method is mandatory');
		}

		if (!$this->url instanceof UrlInterface) {
			throw new RequestBuilderException('url is mandatory');
		}

		return new Request($this->uuid, $this->method, $this->url, $this->headers, $this->body);
	}
}
