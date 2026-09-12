<?php

namespace Hoo\WordPressPluginFramework\Http\Responses;

use Hoo\WordPressPluginFramework\Http\Message\Headers\HeadersInterface;

interface ResponsesBuilderInterface
{
	public function withStatusCode(int $statusCode): static;

	public function withHeaders(HeadersInterface|array $headers): static;

	public function withBodies(mixed $body): static;
	public function withUnnormalizedBodies(mixed $body): static;
	public function withoutBodies(): static;

	public function build(): ResponsesInterface;
}
