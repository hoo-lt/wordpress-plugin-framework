<?php

namespace Hoo\WordPressPluginFramework\Http\Decoders\Query;

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
		parse_str($encoded, $decoded);
		return $decoded;
	}

	public function decodesMediaType(MediaTypeInterface $mediaType): bool
	{
		return false;
	}
}
