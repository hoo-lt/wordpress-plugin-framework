<?php

namespace Hoo\WordPressPluginFramework\Http\Decoders;

use Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;

interface DecoderInterface
{
	public function mediaTypes(): array;

	public function decode(mixed $encoded): mixed;

	public function decodesType(mixed $encoded): bool;
	public function decodesMediaType(MediaTypeInterface $mediaType): bool;
}
