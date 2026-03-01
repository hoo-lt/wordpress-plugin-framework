<?php

namespace Hoo\WordPressPluginFramework\Http\Url;

interface QueryInterface
{
	public static function from(array $query): self;
	public function withValue(string $name, string $value): self;
	public function __toString(): string;
}
