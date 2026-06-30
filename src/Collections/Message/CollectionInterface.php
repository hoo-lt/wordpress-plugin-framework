<?php

namespace Hoo\WordPressPluginFramework\Collections\Message;

use Countable;
use IteratorAggregate;

interface CollectionInterface extends IteratorAggregate, Countable
{
	public function add(string $key, string $message): void;
	public function remove(string $key): void;

	public function all(): array;

	public function isEmpty(): bool;
	public function isNotEmpty(): bool;

	public function toArray(): array;
}
