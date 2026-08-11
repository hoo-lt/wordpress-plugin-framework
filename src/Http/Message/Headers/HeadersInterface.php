<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers;

use Countable;
use IteratorAggregate;

interface HeadersInterface extends IteratorAggregate, Countable
{
	public function header(string $name): ?string;
	public function withHeader(string $name, string $header): static;
	public function withoutHeader(string $name): static;

	public function accept(): ?string;
	public function contentLength(): ?int;
	public function contentType(): ?string;

}
