<?php

namespace Hoo\WordPressPluginFramework\Http\Request;

use Hoo\WordPressPluginFramework\Http\Method\Method;
use Hoo\WordPressPluginFramework\Http\Url\UrlInterface;

interface RequestInterface
{
	public function headers(): array;
	public function withHeaders(array $headers): static;
	public function withoutHeaders(): static;

	public function header(string $name): ?string;
	public function withHeader(string $name, string $header): static;
	public function withoutHeader(string $name): static;

	public function body(): ?string;
	public function withBody(string $body): static;
	public function withoutBody(): static;

	public function method(): Method;
	public function withMethod(Method $method): static;

	public function url(): UrlInterface;
	public function withUrl(UrlInterface $url): static;
}
