<?php

namespace Hoo\WordPressPluginFramework\Helpers\Array;

interface HelperInterface
{
	public function value(array $array, string $key): mixed;
	public function withValue(array $array, string $key, mixed $value): array;
	public function withoutValue(array $array, string $key): array;
}
