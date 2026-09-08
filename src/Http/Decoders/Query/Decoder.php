<?php

namespace Hoo\WordPressPluginFramework\Http\Decoders\Query;

use Hoo\WordPressPluginFramework\{
	Http\Decoders\DecoderException,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
};

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

	public function decode(mixed $encoded): mixed
	{
		if (!$this->decodesType($encoded)) {
			throw new DecoderException('does not decode');
		}

		parse_str($encoded, $decoded);
		return $decoded;
	}

	public function decodesType(mixed $encoded): bool
	{
		if (!is_string($encoded)) {
			return false;
		}

		return true;
	}

	public function decodesMediaType(MediaTypeInterface $mediaType): bool
	{
		return false;
	}
}
