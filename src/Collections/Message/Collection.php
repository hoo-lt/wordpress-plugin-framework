<?php

namespace Hoo\WordPressPluginFramework\Collections\Message;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

class Collection implements IteratorAggregate, Countable
{
	public function __construct(
		protected array $messages = []
	) {
	}

	public function add(string $key, string $message): void
	{
		$this->messages[$key][] = $message;
	}

	public function remove(string $key): void
	{
		unset($this->messages[$key]);
	}

	public function all(): array
	{
		return $this->messages;
	}

	public function any(): bool
	{
		return $this->count() > 0;
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator($this->messages);
	}

	public function count(): int
	{
		return count($this->messages);
	}
}
