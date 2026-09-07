<?php

namespace Hoo\WordPressPluginFramework\Http\Encoders\Query;

use Hoo\WordPressPluginFramework\{
	Http\Encoders\EncoderException,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
};

readonly class Encoder implements EncoderInterface
{
	public function encode(mixed $decoded): string
	{
		if (!$this->encodes($decoded)) {
			throw new EncoderException('does not encode');
		}

		return http_build_query($decoded, '', '&', PHP_QUERY_RFC3986);
	}

	public function encodes(mixed $decoded): bool
	{
		if (!is_array($decoded) && !is_object($decoded)) {
			return false;
		}

		return true;
	}

	public function encodesMediaType(MediaTypeInterface $mediaType): bool
	{
		return true;
	}
}