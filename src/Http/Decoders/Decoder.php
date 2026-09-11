<?php

namespace Hoo\WordPressPluginFramework\Http\Decoders;

use Hoo\WordPressPluginFramework\{
	Http\Message\Headers\ContentType\MediaType\MediaType,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
};

readonly class Decoder implements DecoderInterface
{
	public function __construct(
		protected MediaTypeInterface $mediaType = new MediaType('application', 'octet-stream'),
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
		return $encoded;
	}

	public function decodesMediaType(MediaTypeInterface $mediaType): bool
	{
		return true;
	}
}
