<?php

namespace Hoo\WordPressPluginFramework\Http\Responses;

interface ResponsesBuilderInterface
{
	public function withStatusCode(int $statusCode): static;

	public function withHeaders(array $headers): static;

	public function withHeader(string $name, string $value): static;
	public function withoutHeader(string $name): static;

	public function withBody(string $contentType, mixed $body): static;
	public function withoutBody(string $contentType): static;

	public function withView(string $contentType, string $view, mixed $viewModel): static;
	public function withoutView(string $contentType): static;

	public function build(): ResponsesInterface;
}
