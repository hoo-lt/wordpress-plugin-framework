<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers;

use Countable;
use IteratorAggregate;

interface HeadersInterface extends IteratorAggregate, Countable
{
	public function header(string $key): ?string;
	public function withHeader(string $key, string $header): static;
	public function withoutHeader(string $key): static;

	public function accept(): ?string;
	public function contentType(): ?string;
}
