<?php

namespace Hoo\WordPressPluginFramework\Http\Headers;

interface HeadersInterface
{
	public function values(): array;
	public function value(string $name): ?string;

	public function with(array $headers): static;
	public function without(): static;

	public function withValue(string $name, string $value): static;
	public function withoutValue(string $name): static;
}
