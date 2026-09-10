<?php

namespace Hoo\WordPressPluginFramework\Http\Decoders;

use Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;

readonly class Decoder implements DecoderInterface
{
	public function __construct(
		protected array $mediaTypes,
	) {
	}

	public function mediaTypes(): array
	{
		return $this->mediaTypes;
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
