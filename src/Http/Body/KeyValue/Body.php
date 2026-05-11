<?php

namespace Hoo\WordPressPluginFramework\Http\Body\KeyValue;

use Hoo\WordPressPluginFramework\{
	Helpers,
	Http,
};

readonly class Body implements BodyInterface
{
	public function __construct(
		protected Helpers\KeyValue\HelperInterface $keyValueHelper,
		protected Http\Coders\EncoderInterface $encoder,
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
			$this->encoder,
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
			$this->encoder,
			$this->keyValueHelper->withoutValue(
				$this->body,
				$key,
			),
		);
	}

	public function __toString(): string
	{
		return $this->encoder->encode($this->body);
	}
}