<?php

namespace Hoo\WordPressPluginFramework\Helpers\KeyValue;

interface HelperInterface
{
	public function values(array|object $data, string $path): array;

	public function value(array|object $data, string $path): mixed;
	public function withValue(array|object $data, string $path, mixed $value): array|object;
	public function withoutValue(array|object $data, string $path): array|object;
}
