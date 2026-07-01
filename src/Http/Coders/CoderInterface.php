<?php

namespace Hoo\WordPressPluginFramework\Http\Coders;

interface CoderInterface
{
	public function supports(string $mediaType): bool;

	public function decodes(mixed $encoded): bool;
	public function decode(mixed $encoded): mixed;
	public function tryDecode(mixed $encoded): mixed;

	public function encodes(mixed $decoded): bool;
	public function encode(mixed $decoded): string;
	public function tryEncode(mixed $decoded): ?string;
}