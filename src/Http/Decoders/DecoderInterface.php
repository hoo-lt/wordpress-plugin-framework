<?php

namespace Hoo\WordPressPluginFramework\Http\Decoders;

use Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;

interface DecoderInterface
{
	public function decode(mixed $encoded): mixed;
	public function decodes(mixed $encoded): bool;
	public function decodesMediaType(MediaTypeInterface $mediaType): bool;
}
