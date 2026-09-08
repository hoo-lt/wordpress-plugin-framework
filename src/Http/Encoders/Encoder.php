<?php

namespace Hoo\WordPressPluginFramework\Http\Encoders;

use Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;

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
