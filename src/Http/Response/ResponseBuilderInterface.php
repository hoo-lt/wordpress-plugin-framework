<?php

namespace Hoo\WordPressPluginFramework\Http\Response;

use Hoo\WordPressPluginFramework\Http\Message\Headers\HeadersInterface;

interface ResponseBuilderInterface
{
	public function withStatusCode(int $statusCode): static;

	public function withHeaders(HeadersInterface|array $headers): static;

	public function withBody(mixed $body): static;
	public function withUnnormalizedBody(mixed $body): static;
	public function withoutBody(): static;

	public function build(): ResponseInterface;
}
