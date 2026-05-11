<?php

namespace Hoo\WordPressPluginFramework\Http\Body\KeyValue;

use Hoo\WordPressPluginFramework\Http;

interface BodyInterface extends Http\Body\BodyInterface
{
	public function values(string $key): array;

	public function value(string $key): mixed;
	public function withValue(string $key, mixed $value): static;
	public function withoutValue(string $key): static;
}
