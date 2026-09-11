<?php

namespace Hoo\WordPressPluginFramework\Http\Decoders\Form;

use Hoo\WordPressPluginFramework\{
	Http\Decoders\DecoderException,
	Http\Decoders\DecoderInterface,
	Http\Message\Headers\ContentType\MediaType\MediaType,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
};

readonly class Decoder implements DecoderInterface
{
	public function __construct(
		protected MediaTypeInterface $mediaType = new MediaType('application', 'x-www-form-urlencoded'),
	) {
		if (!$this->decodesMediaType($mediaType)) {
			throw new DecoderException('does not decode this media type');
		}
	}

	public function mediaType(): MediaTypeInterface
	{
		return $this->mediaType;
	}

	public function withMediaType(MediaTypeInterface $mediaType): static
	{
		return new static($mediaType);
	}

	public function decode(string $encoded): mixed
	{
		parse_str($encoded, $decoded);
		return $decoded;
	}

	public function decodesMediaType(MediaTypeInterface $mediaType): bool
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
