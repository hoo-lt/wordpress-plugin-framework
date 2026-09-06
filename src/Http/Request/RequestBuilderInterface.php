<?php

namespace Hoo\WordPressPluginFramework\Http\Request;

interface RequestBuilderInterface
{
	public function withMethod(string $method): static;

	public function withUrl(string $url): static;

	public function withHeaders(array $headers): static;

	public function withHeader(string $name, string $value): static;
	public function withoutHeader(string $name): static;

	public function withBody(string $contentType, mixed $body): static;
	public function withoutBody(): static;

	public function withView(string $contentType, string $view, mixed $viewModel): static;
	public function withoutView(): static;

	public function build(): RequestInterface;
}
