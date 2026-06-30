<?php

namespace Hoo\WordPressPluginFramework\Http\Coders;

interface CoderInterface
{
	public function decode(string $encoded): array;
	public function tryDecode(?string $encoded): ?array;

	public function encode(array|object $decoded): string;
	public function tryEncode(array|object|null $decoded): ?string;
}