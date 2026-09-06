<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body;

use Hoo\WordPressPluginFramework\{
	Http\Accessor\AccessorInterface,
	Http\Encoders\EncoderInterface,
	Http\Message\Body\BodyInterface,
};
use stdClass;

readonly class Body implements BodyInterface
{
	public function __construct(
		protected AccessorInterface $accessor,
		protected EncoderInterface $encoder,
		protected mixed $body,
	) {
	}

	public function values(string $key): array
	{
		return $this->accessor->values($this->body, $key);
	}

	public function has(string $key): bool
	{
		return $this->accessor->has($this->body, $key);
	}

	public function get(string $key): string|int|float|bool|null|array|stdClass
	{
		return $this->accessor->get($this->body, $key);
	}

	public function with(string $key, string|int|float|bool|null|array|stdClass $value): static
	{
		$value = $this->accessor->with($this->body, $key, $value);

		return new static($this->accessor, $this->encoder, $value);
	}

	public function without(string $key): static
	{
		$value = $this->accessor->without($this->body, $key);

		return new static($this->accessor, $this->encoder, $value);
	}

	public function __toString(): string
	{
		return $this->encoder->encode($this->body);
	}
}