<?php

namespace Hoo\WordPressPluginFramework\Http\Coders;

use Hoo\WordPressPluginFramework\Http\Semantics\ContentType\MediaType\MediaTypeInterface;

readonly class CoderFactory implements CoderFactoryInterface
{
	public function __construct(
		protected array $coders,
	) {
	}

	public function createDecoder(mixed $encoded, MediaTypeInterface $mediaType): CoderInterface
	{
		$coder = $this->tryCreateDecoder($encoded, $mediaType);
		if ($coder === null) {
			throw new CoderFactoryException('no coder decodes this media type');
		}

		return $coder;
	}

	public function tryCreateDecoder(mixed $encoded, MediaTypeInterface $mediaType): ?CoderInterface
	{
		foreach ($this->coders as $coder) {
			if (
				$coder->codes($mediaType) &&
				$coder->decodes($encoded)
			) {
				return $coder;
			}
		}

		return null;
	}

	public function createEncoder(mixed $decoded, MediaTypeInterface $mediaType): CoderInterface
	{
		$coder = $this->tryCreateEncoder($decoded, $mediaType);
		if ($coder === null) {
			throw new CoderFactoryException('no coder encodes this value for this media type');
		}

		return $coder;
	}

	public function tryCreateEncoder(mixed $decoded, MediaTypeInterface $mediaType): ?CoderInterface
	{
		foreach ($this->coders as $coder) {
			if (
				$coder->codes($mediaType) &&
				$coder->encodes($decoded)
			) {
				return $coder;
			}
		}

		return null;
	}
}
