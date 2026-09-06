<?php

namespace Hoo\WordPressPluginFramework\Http\Decoders;

use Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;

readonly class Decoder implements DecoderInterface
{
	public function decode(mixed $encoded): mixed
	{
		if (!$this->decodes($encoded)) {
			throw new DecoderException('does not decode');
		}

		return $encoded;
	}

	public function decodes(mixed $encoded): bool
	{
		if (!is_string($encoded)) {
			return false;
		}

		return true;
	}

	public function decodesMediaType(MediaTypeInterface $mediaType): bool
	{
		return true;
	}
}