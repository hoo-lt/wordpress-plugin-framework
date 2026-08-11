<?php

namespace Hoo\WordPressPluginFramework\Http\Response;

interface ResponseBuilderInterface
{
	public function withStatusCode(int $statusCode): static;

	public function withHeader(string $name, string $value): static;
	public function withoutHeader(string $name): static;

	public function withBody(mixed $body): static;
	public function withoutBody(): static;

	public function build(): ResponseInterface;
}
