<?php

namespace Hoo\WordPressPluginFramework\Helpers\Array;

interface HelperInterface
{
	public function value(array $array, string $key): mixed;
	/**
	 * @return array<string, mixed> map of resolved dot-path => value.
	 *                              Literal segments produce exactly one pair (value `null` if missing).
	 *                              Wildcard (`*`) segments enumerate existing entries.
	 */
	public function values(array $array, string $key): array;
	public function withValue(array $array, string $key, mixed $value): array;
	public function withoutValue(array $array, string $key): array;
}
