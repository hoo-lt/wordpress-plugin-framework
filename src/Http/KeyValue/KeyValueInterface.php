<?php

namespace Hoo\WordPressPluginFramework\Http\KeyValue;

interface KeyValueInterface
{
	public function values(string $key): array;

	public function value(string $key): mixed;
	public function withValue(string $key, mixed $value): static;
	public function withoutValue(string $key): static;
}
