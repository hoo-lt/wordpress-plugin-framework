<?php

namespace Hoo\WordPressPluginFramework\Http\Encoders;

use Hoo\WordPressPluginFramework\{
	Http\Message\Headers\ContentType\MediaType\MediaType,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
};

readonly class Encoder implements EncoderInterface
{
	public function __construct(
		protected MediaTypeInterface $mediaType = new MediaType('application', 'octet-stream'),
	) {
		if (!$this->encodesMediaType($mediaType)) {
			throw new EncoderException('does not encode this media type');
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

	public function encode(mixed $decoded): string
	{
		if (!$this->encodesType($decoded)) {
			throw new EncoderException('does not encode');
		}

		return $decoded;
	}

	public function encodesType(mixed $decoded): bool
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
