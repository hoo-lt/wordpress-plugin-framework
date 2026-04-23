<?php

namespace Hoo\WordPressPluginFramework\Http\Request\Body;

use Hoo\WordPressPluginFramework\Helpers;

readonly class Body implements BodyInterface
{
	public function __construct(
		protected Helpers\Array\HelperInterface $arrayHelper,
		protected array $values,
	) {
	}

	public function with(array $values): static
	{
		return new static($this->arrayHelper, $values);
	}

	public function without(): static
	{
		return new static($this->arrayHelper, []);
	}

	public function values(): array
	{
		return $this->values;
	}

	public function value(string $key): mixed
	{
		return $this->arrayHelper->value($this->values, $key);
	}

	public function withValue(string $key, mixed $value): static
	{
		return new static(
			$this->arrayHelper,
			$this->arrayHelper->withValue($this->values, $key, $value)
		);
	}

	public function withoutValue(string $key): static
	{
		return new static(
			$this->arrayHelper,
			$this->arrayHelper->withoutValue($this->values, $key)
		);
	}
}