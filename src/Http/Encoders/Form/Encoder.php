<?php

namespace Hoo\WordPressPluginFramework\Http\Encoders\Form;

use Hoo\WordPressPluginFramework\{
	Http\Encoders\EncoderException,
	Http\Encoders\EncoderInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
};
use stdClass;

readonly class Encoder implements EncoderInterface
{
	public function __construct(
		protected array $mediaTypes,
	) {
	}

	public function mediaTypes(): array
	{
		return $this->mediaTypes;
	}

	public function encode(mixed $decoded): string
	{
		if (!$this->encodesType($decoded)) {
			throw new EncoderException('does not encode');
		}

		return http_build_query($decoded, '', '&', PHP_QUERY_RFC1738);
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
		$type = $mediaType->type();
		if ($type !== 'application') {
			return false;
		}

		$subtype = $mediaType->subtype();
		if ($subtype !== 'x-www-form-urlencoded') {
			return false;
		}

		return true;
	}
}
