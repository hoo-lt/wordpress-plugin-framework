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
		foreach ($this->coders as $coder) {
			if (
				$coder->decodes($encoded) &&
				$coder->codes($mediaType)
			) {
				return $coder;
			}
		}

		throw new CoderFactoryException('no coder decodes this media type');
	}

	public function createEncoder(mixed $decoded, MediaTypeInterface $mediaType): CoderInterface
	{
		foreach ($this->coders as $coder) {
			if (
				$coder->encodes($decoded) &&
				$coder->codes($mediaType)
			) {
				return $coder;
			}
		}

		throw new CoderFactoryException('no coder encodes this value for this media type');
	}
}
