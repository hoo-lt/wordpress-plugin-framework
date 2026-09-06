<?php

namespace Hoo\WordPressPluginFramework\Http\Encoders;

use Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;

readonly class Encoder implements EncoderInterface
{
	public function encode(mixed $decoded): string
	{
		if (!$this->encodes($decoded)) {
			throw new EncoderException('does not encode');
		}

		return $decoded;
	}

	public function encodes(mixed $decoded): bool
	{
		if (!is_string($decoded)) {
			return false;
		}

		return true;
	}

	public function encodesMediaType(MediaTypeInterface $mediaType): bool
	{
		return true;
	}
}