<?php

namespace Hoo\WordPressPluginFramework\Http\Request;

use Hoo\WordPressPluginFramework\{
	Http\Message\Headers\HeadersInterface,
	Http\Method\Method,
	Http\Url\UrlInterface,
};

interface RequestBuilderInterface
{
	public function withMethod(Method|string $method): static;

	public function withUrl(UrlInterface|string $url): static;

	public function withHeaders(HeadersInterface|array $headers): static;

	public function withBody(mixed $body): static;
	public function withUnnormalizedBody(mixed $body): static;
	public function withoutBody(): static;

	public function build(): RequestInterface;
}
