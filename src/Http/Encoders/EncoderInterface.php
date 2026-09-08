<?php

namespace Hoo\WordPressPluginFramework\Http\Encoders;

use Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;

interface EncoderInterface
{
	public function mediaTypes(): array;

	public function encode(mixed $decoded): string;

	public function encodesType(mixed $decoded): bool;
	public function encodesMediaType(MediaTypeInterface $mediaType): bool;
}
