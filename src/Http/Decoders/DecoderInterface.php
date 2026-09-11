<?php

namespace Hoo\WordPressPluginFramework\Http\Decoders;

use Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;

interface DecoderInterface
{
	public function mediaType(): MediaTypeInterface;
	public function withMediaType(MediaTypeInterface $mediaType): static;

	public function decode(string $encoded): mixed;

	public function decodesMediaType(MediaTypeInterface $mediaType): bool;
}
