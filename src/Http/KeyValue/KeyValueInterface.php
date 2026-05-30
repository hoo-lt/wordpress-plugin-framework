<?php

namespace Hoo\WordPressPluginFramework\Http\KeyValue;

use Countable;
use IteratorAggregate;

interface KeyValueInterface extends IteratorAggregate, Countable
{
	public function values(string $key): array;

	public function value(string $key): mixed;
	public function withValue(string $key, mixed $value): static;
	public function withoutValue(string $key): static;

	public function toArray(): array;
}
