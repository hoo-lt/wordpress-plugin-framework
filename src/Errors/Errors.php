<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Errors;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

class Errors implements IteratorAggregate, Countable
{
	public function __construct(
		protected array $errors = []
	) {
	}

	public function add(string $key, string $error): void
	{
		$this->errors[$key][] = $error;
	}

	public function remove(string $key): void
	{
		unset($this->errors[$key]);
	}

	public function all(): array
	{
		return $this->errors;
	}

	public function any(): bool
	{
		return $this->count() > 0;
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator($this->errors);
	}

	public function count(): int
	{
		return count($this->errors);
	}
}
