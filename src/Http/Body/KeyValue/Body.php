<?php

namespace Hoo\WordPressPluginFramework\Http\Body\KeyValue;

use ArrayIterator;
use Hoo\WordPressPluginFramework\{
	Helpers,
	Http,
};
use Traversable;

readonly class Body implements BodyInterface
{
	public function __construct(
		protected Helpers\KeyValue\HelperInterface $keyValueHelper,
		protected Http\Coders\CoderInterface $coder,
		protected array $body,
	) {
	}

	public function values(string $key): array
	{
		return $this->keyValueHelper->values(
			$this->body,
			$key,
		);
	}

	public function value(string $key): mixed
	{
		return $this->keyValueHelper->value(
			$this->body,
			$key,
		);
	}

	public function withValue(string $key, mixed $value): static
	{
		return new static(
			$this->keyValueHelper,
			$this->coder,
			$this->keyValueHelper->withValue(
				$this->body,
				$key,
				$value,
			),
		);
	}

	public function withoutValue(string $key): static
	{
		return new static(
			$this->keyValueHelper,
			$this->coder,
			$this->keyValueHelper->withoutValue(
				$this->body,
				$key,
			),
		);
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator($this->body);
	}

	public function count(): int
	{
		return count($this->body);
	}

	public function __toString(): string
	{
		return $this->coder->encode($this->body);
	}
}