<?php

namespace Hoo\WordPressPluginFramework\Http\Encoders\Json;

use Hoo\WordPressPluginFramework\{
	Http\Abnf\Rfc6838,
	Http\Encoders\EncoderException,
	Http\Encoders\EncoderInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
};
use Throwable;

readonly class Encoder implements EncoderInterface
{
	public function encode(mixed $decoded): string
	{
		if (!$this->encodes($decoded)) {
			throw new EncoderException('does not encode');
		}

		try {
			return json_encode($decoded, JSON_THROW_ON_ERROR, 512);
		} catch (Throwable $throwable) {
			throw new EncoderException($throwable->getMessage());
		}
	}

	public function encodes(mixed $decoded): bool
	{
		if (is_resource($decoded)) {
			return false;
		}

		return true;
	}

	public function encodesMediaType(MediaTypeInterface $mediaType): bool
	{
		$type = $mediaType->type();
		if ($type !== 'application') {
			return false;
		}

		$subtype = $mediaType->subtype();
		if ($subtype !== 'json' && preg_match('/\A' . Rfc6838::RESTRICTED_NAME . '\+json\z/', $subtype) !== 1) {
			return false;
		}

		return true;
	}
}
