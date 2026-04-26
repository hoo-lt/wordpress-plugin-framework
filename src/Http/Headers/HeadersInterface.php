<?php

namespace Hoo\WordPressPluginFramework\Http\Headers;

interface HeadersInterface
{
	public function __invoke(): array;

	public function values(): array;

	public function value(string $key): mixed;
	public function withValue(string $key, mixed $value): static;
	public function withoutValue(string $key): static;
}
