<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers;

use Countable;
use IteratorAggregate;

interface HeadersInterface extends IteratorAggregate, Countable
{
	public function has(string $name): bool;
	public function get(string $name): ?string;

	public function with(string $name, string $value): static;
	public function without(string $name): static;
}
