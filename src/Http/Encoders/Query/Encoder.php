<?php

namespace Hoo\WordPressPluginFramework\Http\Encoders\Query;

use Hoo\WordPressPluginFramework\{
	Http\Encoders\EncoderException,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
};
use stdClass;

readonly class Encoder implements EncoderInterface
{
	public function mediaType(): ?MediaTypeInterface
	{
		return null;
	}

	public function encode(mixed $decoded): string
	{
		if (!$this->encodesType($decoded)) {
			throw new EncoderException('does not encode');
		}

		return http_build_query($decoded, '', '&', PHP_QUERY_RFC3986);
	}

	public function encodesType(mixed $decoded): bool
	{
		if (!is_array($decoded) && !$decoded instanceof stdClass) {
			return false;
		}

		return true;
	}

	public function encodesMediaType(MediaTypeInterface $mediaType): bool
	{
		return false;
	}
}
