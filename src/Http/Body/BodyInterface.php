<?php

namespace Hoo\WordPressPluginFramework\Http\Body;

interface BodyInterface
{
	public function values(string $key = ''): array;

	public function value(string $key = ''): mixed;
	public function withValue(string $key, mixed $value): static;
	public function withoutValue(string $key): static;

	public function __toString(): string;
}
