<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Collections\Rule;

use ArrayIterator;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Rules\RuleInterface;
use Traversable;

readonly class Collection
{
	public function __construct(
		protected array $rules = []
	) {
	}

	public function with(RuleInterface ...$rules): static
	{
		return new static([
			...$this->rules,
			...$rules,
		]);
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator($this->rules);
	}

	public function count(): int
	{
		return count($this->rules);
	}
}
