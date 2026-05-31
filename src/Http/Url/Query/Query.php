<?php

namespace Hoo\WordPressPluginFramework\Http\Url\Query;

use ArrayIterator;
use Hoo\WordPressPluginFramework\{
	Helpers\KeyValue\HelperInterface,
	Http\Coders\CoderInterface,
	Http\KeyValue\KeyValueInterface,
};
use Traversable;

readonly class Query implements QueryInterface, KeyValueInterface
{
	public function __construct(
		protected HelperInterface $helper,
		protected CoderInterface $coder,
		protected array $query,
	) {
	}

	public function values(string $key): array
	{
		return $this->helper->values($this->query, $key);
	}

	public function value(string $key): mixed
	{
		return $this->helper->value($this->query, $key);
	}

	public function withValue(string $key, mixed $value): static
	{
		return new static(
			$this->helper,
			$this->coder,
			$this->helper->withValue($this->query, $key, $value)
		);
	}

	public function withoutValue(string $key): static
	{
		return new static(
			$this->helper,
			$this->coder,
			$this->helper->withoutValue($this->query, $key)
		);
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator($this->query);
	}

	public function count(): int
	{
		return count($this->query);
	}

	public function __toString(): string
	{
		return $this->coder->encode($this->query);
	}
}