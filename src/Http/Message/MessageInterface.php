<?php

namespace Hoo\WordPressPluginFramework\Http\Message;

use Hoo\WordPressPluginFramework\{
	Http\Message\Body\BodyInterface,
	Http\Message\Headers\HeadersInterface,
};
use Closure;

interface MessageInterface
{
	public function headers(): HeadersInterface;
	public function withHeaders(HeadersInterface|Closure $headers): static;

	public function body(): ?BodyInterface;
	public function withBody(BodyInterface|Closure $body): static;
	public function withoutBody(): static;
}
