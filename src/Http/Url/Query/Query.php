<?php

namespace Hoo\WordPressPluginFramework\Http\Url\Query;

use Hoo\WordPressPluginFramework\{
	Http\Accessor\AccessorInterface,
	Http\Encoders\Query\EncoderInterface,
};
use stdClass;

readonly class Query implements QueryInterface
{
	public function __construct(
		protected AccessorInterface $accessor,
		protected EncoderInterface $encoder,
		protected mixed $query,
	) {
	}

	public function values(string $key): array
	{
		return $this->accessor->values($this->query, $key);
	}

	public function has(string $key): bool
	{
		return $this->accessor->has($this->query, $key);
	}

	public function get(string $key): string|int|float|bool|null|array|stdClass
	{
		return $this->accessor->get($this->query, $key);
	}

	public function with(string $key, string|int|float|bool|null|array|stdClass $value): static
	{
		$value = $this->accessor->with($this->query, $key, $value);

		return new static($this->accessor, $this->encoder, $value);
	}

	public function without(string $key): static
	{
		$value = $this->accessor->without($this->query, $key);

		return new static($this->accessor, $this->encoder, $value);
	}

	public function __toString(): string
	{
		return $this->encoder->encode($this->query);
	}
}