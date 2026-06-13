<?php

namespace Hoo\WordPressPluginFramework\View;

interface ViewInterface
{
	public function values(): array;
	public function withValues(array $values): static;
	public function withoutValues(): static;

	public function value(string $key): mixed;
	public function withValue(string $key, mixed $value): static;
	public function withoutValue(string $key): static;

	public function has(string $view): bool;
	public function get(string $view): string;
}