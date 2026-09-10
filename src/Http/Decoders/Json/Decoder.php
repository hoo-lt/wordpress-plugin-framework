<?php

namespace Hoo\WordPressPluginFramework\Http\Decoders\Json;

use Hoo\WordPressPluginFramework\{
	Http\Abnf\Rfc6838,
	Http\Decoders\DecoderException,
	Http\Decoders\DecoderInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
};
use Throwable;

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
		try {
			return json_decode($encoded, false, 512, JSON_THROW_ON_ERROR);
		} catch (Throwable $throwable) {
			throw new DecoderException($throwable->getMessage());
		}
	}

	public function decodesMediaType(MediaTypeInterface $mediaType): bool
	{
		$type = $mediaType->type();
		if ($type !== 'application') {
			return false;
		}

		$subtype = $mediaType->subtype();
		if (
			$subtype !== 'json' &&
			preg_match('/\A' . Rfc6838::RESTRICTED_NAME . '\+json\z/', $subtype) !== 1
		) {
			return false;
		}

		return true;
	}
}
