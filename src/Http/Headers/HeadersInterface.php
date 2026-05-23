<?php

namespace Hoo\WordPressPluginFramework\Http\Headers;

use Countable;
use IteratorAggregate;

interface HeadersInterface extends IteratorAggregate, Countable
{
	public function header(string $key): mixed;
	public function withHeader(string $key, mixed $header): static;
	public function withoutHeader(string $key): static;

	public function accept(): ?string;
	public function contentLength(): ?int;
	public function contentType(): ?string;
}
