<?php

namespace Hoo\WordPressPluginFramework\Http\Decoders\Form;

use Hoo\WordPressPluginFramework\{
	Http\Decoders\DecoderInterface,
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
