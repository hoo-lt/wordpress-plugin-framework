<?php

namespace Hoo\WordPressPluginFramework\Http\Response;

interface ResponseBuilderInterface
{
	public function withStatusCode(int $statusCode): static;

	public function withHeaders(array $headers): static;

	public function withHeader(string $name, string $value): static;
	public function withoutHeader(string $name): static;

	public function withBody(string $contentType, mixed $body): static;
	public function withUnnormalizedBody(string $contentType, mixed $body): static;
	public function withoutBody(): static;

	public function build(): ResponseInterface;
}
