<?php

namespace Hoo\WordPressPluginFramework\Http\Client\Request;

interface RequestBuilderInterface
{
	public function withMethod(string $method): static;

	public function withUrl(string $url): static;

	public function withHeader(string $name, string $value): static;
	public function withoutHeader(string $name): static;

	public function withBody(mixed $body): static;
	public function withoutBody(): static;

	public function build(): RequestInterface;
}
