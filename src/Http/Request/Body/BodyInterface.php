<?php

namespace Hoo\WordPressPluginFramework\Http\Request\Body;

interface BodyInterface
{
	public function with(array $values): static;
	public function without(): static;

	public function values(): array;

	public function value(string $key): mixed;
	public function withValue(string $key, mixed $value): static;
	public function withoutValue(string $key): static;
}
