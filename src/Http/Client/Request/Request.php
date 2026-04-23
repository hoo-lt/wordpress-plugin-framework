<?php

namespace Hoo\WordPressPluginFramework\Http\Client\Request;

use Hoo\WordPressPluginFramework\Http\Headers\HeadersInterface;
use Hoo\WordPressPluginFramework\Http\Method\Method;
use Hoo\WordPressPluginFramework\Http\Url\UrlInterface;

readonly class Request implements RequestInterface
{
	public function __construct(
		protected HeadersInterface $headers,
		protected ?string $body,
		protected Method $method,
		protected UrlInterface $url,
	) {
	}

	public function headers(): array
	{
		return $this->headers->values();
	}

	public function withHeaders(array $headers): static
	{
		return new static(
			$this->headers->with($headers),
			$this->body,
			$this->method,
			$this->url,
		);
	}

	public function withoutHeaders(): static
	{
		return new static(
			$this->headers->without(),
			$this->body,
			$this->method,
			$this->url,
		);
	}

	public function header(string $name): ?string
	{
		return $this->headers->value($name);
	}

	public function withHeader(string $name, string $header): static
	{
		return new static(
			$this->headers->withValue($name, $header),
			$this->body,
			$this->method,
			$this->url,
		);
	}

	public function withoutHeader(string $name): static
	{
		return new static(
			$this->headers->withoutValue($name),
			$this->body,
			$this->method,
			$this->url,
		);
	}

	public function body(): ?string
	{
		return $this->body;
	}

	public function withBody(string $body): static
	{
		return new static(
			$this->headers,
			$body,
			$this->method,
			$this->url,
		);
	}

	public function withoutBody(): static
	{
		return new static(
			$this->headers,
			null,
			$this->method,
			$this->url,
		);
	}

	public function method(): Method
	{
		return $this->method;
	}

	public function withMethod(Method $method): static
	{
		return new static(
			$this->headers,
			$this->body,
			$method,
			$this->url,
		);
	}

	public function url(): UrlInterface
	{
		return $this->url;
	}

	public function withUrl(UrlInterface $url): static
	{
		return new static(
			$this->headers,
			$this->body,
			$this->method,
			$url,
		);
	}
}
