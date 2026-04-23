<?php

namespace Hoo\WordPressPluginFramework\Http\Url\Query;

interface QueryInterface
{
	public function with(array $query): static;
	public function without(): static;

	public function values(): array;

	public function value(string $key): mixed;
	public function withValue(string $key, mixed $value): static;
	public function withoutValue(string $key): static;

	public function __toString(): string;
}
