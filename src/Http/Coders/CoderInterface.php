<?php

namespace Hoo\WordPressPluginFramework\Http\Coders;

interface CoderInterface
{
	public function decode(string $string): array;
	public function tryDecode(string $string): ?array;

	public function encode(array $array): string;
	public function tryEncode(array $array): ?string;
}