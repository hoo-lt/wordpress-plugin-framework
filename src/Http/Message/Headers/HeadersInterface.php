<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers;

use Countable;
use Hoo\WordPressPluginFramework\{
	Http\Message\Headers\Accept\AcceptInterface,
	Http\Message\Headers\ContentType\ContentTypeInterface,
};
use IteratorAggregate;

interface HeadersInterface extends IteratorAggregate, Countable
{
	public function has(string $name): bool;
	public function get(string $name): ?string;

	public function with(string $name, string $value): static;
	public function without(string $name): static;

	public function accept(): ?AcceptInterface;
	public function withAccept(AcceptInterface $accept): static;
	public function withoutAccept(): static;

	public function contentType(): ?ContentTypeInterface;
	public function withContentType(ContentTypeInterface $contentType): static;
	public function withoutContentType(): static;
}
