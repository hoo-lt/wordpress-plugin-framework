<?php

namespace Hoo\WordPressPluginFramework\Http\Url\Query;

use ArrayIterator;
use Hoo\WordPressPluginFramework\{
	Helpers,
	Http,
};
use Traversable;

readonly class Query implements QueryInterface
{
	public function __construct(
		protected Helpers\KeyValue\HelperInterface $keyValueHelper,
		protected Http\Coders\Query\CoderInterface $coder,
		protected array $query,
	) {
	}

	public function values(string $key): array
	{
		return $this->keyValueHelper->values(
			$this->query,
			$key
		);
	}

	public function value(string $key): mixed
	{
		return $this->keyValueHelper->value(
			$this->query,
			$key
		);
	}

	public function withValue(string $key, mixed $value): static
	{
		return new static(
			$this->keyValueHelper,
			$this->coder,
			$this->keyValueHelper->withValue(
				$this->query,
				$key,
				$value
			)
		);
	}

	public function withoutValue(string $key): static
	{
		return new static(
			$this->keyValueHelper,
			$this->coder,
			$this->keyValueHelper->withoutValue(
				$this->query,
				$key
			)
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