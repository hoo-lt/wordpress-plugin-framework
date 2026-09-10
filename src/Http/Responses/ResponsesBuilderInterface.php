<?php

namespace Hoo\WordPressPluginFramework\Http\Responses;

interface ResponsesBuilderInterface
{
	public function withStatusCode(int $statusCode): static;

	public function withHeaders(array $headers): static;
	public function withoutHeaders(): static;

	public function withHeader(string $name, string $value): static;
	public function withoutHeader(string $name): static;

	public function withBodies(mixed $body): static;
	public function withUnnormalizedBodies(mixed $body): static;
	public function withoutBodies(): static;

	public function build(): ResponsesInterface;
}
