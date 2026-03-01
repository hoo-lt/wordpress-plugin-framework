<?php

namespace Hoo\WordPressPluginFramework\Http\Url;

interface QueryInterface
{
	public static function from(string $query): self;
	public function value(string $key): string;
	public function withValue(string $key, string $value): self;
	public function __toString(): string;
}
