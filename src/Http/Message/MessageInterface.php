<?php

namespace Hoo\WordPressPluginFramework\Http\Message;

use Hoo\WordPressPluginFramework\{
	Http\Message\Body\BodyInterface,
	Http\Message\Headers\HeadersInterface,
};

interface MessageInterface
{
	public function headers(): ?HeadersInterface;
	public function withHeaders(HeadersInterface $headers): static;
	public function withoutHeaders(): static;

	public function header(string $key): mixed;

	public function body(): ?BodyInterface;
	public function withBody(BodyInterface $body): static;
	public function withoutBody(): static;

	public function bodyValues(string $key): ?array;
	public function bodyValue(string $key): mixed;
}
