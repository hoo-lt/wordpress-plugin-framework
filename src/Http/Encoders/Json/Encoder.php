<?php

namespace Hoo\WordPressPluginFramework\Http\Encoders\Json;

use Hoo\WordPressPluginFramework\{
	Http\Abnf\Rfc6838,
	Http\Encoders\EncoderException,
	Http\Encoders\EncoderInterface,
	Http\Message\Headers\ContentType\MediaType\MediaTypeInterface,
};
use stdClass;
use Throwable;

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

		try {
			return json_encode($decoded, JSON_THROW_ON_ERROR, 512);
		} catch (Throwable $throwable) {
			throw new EncoderException($throwable->getMessage());
		}
	}

	public function encodesType(mixed $decoded): bool
	{
		if (is_resource($decoded)) {
			return false;
		}

		if (is_object($decoded) && !$decoded instanceof stdClass) {
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
		if ($subtype !== 'json' && preg_match('/\A' . Rfc6838::RESTRICTED_NAME . '\+json\z/', $subtype) !== 1) {
			return false;
		}

		return true;
	}
}
