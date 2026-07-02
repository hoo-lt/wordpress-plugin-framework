<?php

namespace Hoo\WordPressPluginFramework\Http\Coders;

interface CoderInterface
{
	public function codes(string $mediaType): bool;
	public function mediaTypes(): array;

	public function decodes(mixed $encoded): bool;
	public function decode(mixed $encoded): mixed;

	public function encodes(mixed $decoded): bool;
	public function encode(mixed $decoded): string;
}