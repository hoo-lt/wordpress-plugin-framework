<?php

namespace Hoo\WordPressPluginFramework\Http\Message;

use Hoo\WordPressPluginFramework\Http;

interface MessageInterface
{
	public function headers(): ?Http\Headers\HeadersInterface;
	public function withHeaders(Http\Headers\HeadersInterface $headers): static;
	public function withoutHeaders(): static;

	public function body(): ?Http\Body\BodyInterface;
	public function withBody(Http\Body\BodyInterface $body): static;
	public function withoutBody(): static;
}
