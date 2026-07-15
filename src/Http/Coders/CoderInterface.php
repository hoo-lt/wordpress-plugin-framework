<?php

namespace Hoo\WordPressPluginFramework\Http\Coders;

use Hoo\WordPressPluginFramework\Http\Semantics\ContentType\MediaType\MediaTypeInterface;

interface CoderInterface
{
	public function mediaTypes(): array;

	public function codes(MediaTypeInterface $mediaType): bool;

	public function decodes(mixed $encoded): bool;
	public function decode(mixed $encoded): mixed;

	public function encodes(mixed $decoded): bool;
	public function encode(mixed $decoded): string;
}
