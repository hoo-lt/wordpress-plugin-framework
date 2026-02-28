<?php

namespace Hoo\WordPressPluginFramework\Collection;

use ArrayIterator;
use IteratorAggregate;
use Traversable;

abstract class AbstractCollection implements IteratorAggregate
{
	protected array $items = [];

	public function has(Item\Key\KeyInterface $key): bool
	{
		return isset($this->items[$key()]);
	}

	public function get(Item\Key\KeyInterface $key): mixed
	{
		if (!$this->has($key)) {
			return null;
		}

		return $this->items[$key()];
	}

	public function first(): mixed
	{
		if (!$this->items) {
			return null;
		}

		$key = array_key_first($this->items);
		return $this->items[$key];
	}

	public function last(): mixed
	{
		if (!$this->items) {
			return null;
		}

		$key = array_key_last($this->items);
		return $this->items[$key];
	}

	public function remove(Item\Key\KeyInterface $key): void
	{
		if (!$this->has($key)) {
			return;
		}

		unset($this->items[$key()]);
	}

	public function all(): array
	{
		return array_values($this->items);
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator(array_values($this->items));
	}
}