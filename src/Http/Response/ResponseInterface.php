<?php

namespace Hoo\WordPressPluginFramework\Http\Response;

interface ResponseInterface
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

	public function statusCode(): int;
	public function withStatusCode(int $statusCode): static;
}
