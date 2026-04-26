<?php

namespace Hoo\WordPressPluginFramework\Http\Request;

use Hoo\WordPressPluginFramework\Http\{
	Body,
	Headers,
	Method,
	Url,
};

readonly class Request
{
	public function __construct(
		protected Method\Method $method,
		protected Url\UrlInterface $url,
		protected Headers\HeadersFactoryInterface $headersFactory,
		protected ?Headers\HeadersInterface $headers,
		protected Body\BodyFactoryInterface $bodyFactory,
		protected ?Body\BodyInterface $body,
	) {
	}

	public function method(): Method\Method
	{
		return $this->method;
	}

	public function withMethod(Method\Method $method): static
	{
		return new static(
			$method,
			$this->url,
			$this->headersFactory,
			$this->headers,
			$this->bodyFactory,
			$this->body,
		);
	}

	public function url(): Url\UrlInterface
	{
		return $this->url;
	}

	public function withUrl(Url\UrlInterface $url): static
	{
		return new static(
			$this->method,
			$url,
			$this->headersFactory,
			$this->headers,
			$this->bodyFactory,
			$this->body,
		);
	}

	public function headers(): ?array
	{
		return $this->headers ? ($this->headers)() : null;
	}

	public function withHeaders(array $headers): static
	{
		return new static(
			$this->method,
			$this->url,
			$this->headersFactory,
			$this->headersFactory->from($headers),
			$this->bodyFactory,
			$this->body,
		);
	}

	public function withoutHeaders(): static
	{
		return new static(
			$this->method,
			$this->url,
			$this->headersFactory,
			null,
			$this->bodyFactory,
			$this->body,
		);
	}

	public function header(string $name): ?string
	{
		return $this->headers->value($name);
	}

	public function withHeader(string $name, string $header): static
	{
		return new static(
			$this->method,
			$this->url,
			$this->headersFactory,
			$this->headers->withValue($name, $header),
			$this->bodyFactory,
			$this->body,
		);
	}

	public function withoutHeader(string $name): static
	{
		return new static(
			$this->method,
			$this->url,
			$this->headersFactory,
			$this->headers->withoutValue($name),
			$this->bodyFactory,
			$this->body,
		);
	}






	public function body(): ?Body\BodyInterface
	{
		return $this->body;
	}

	public function withBody(Body\BodyInterface $body): static
	{
		return new static(
			$this->method,
			$this->url,
			$this->headersFactory,
			$this->headers,
			$this->bodyFactory,
			$body,
		);
	}

	public function withJsonBody(string $body): static
	{
		return new static(
			$this->method,
			$this->url,
			$this->headersFactory,
			$this->headers,
			$this->bodyFactory,
			$this->bodyFactory->jsonBody($body),
		);
	}

	public function withFormBody(string $body): static
	{
		return new static(
			$this->method,
			$this->url,
			$this->headersFactory,
			$this->headers,
			$this->bodyFactory,
			$this->bodyFactory->formBody($body),
		);
	}

	public function withoutBody(): static
	{
		return new static(
			$this->method,
			$this->url,
			$this->headersFactory,
			$this->headers,
			$this->bodyFactory,
			null,
		);
	}
}
