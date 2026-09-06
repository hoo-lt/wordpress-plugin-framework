<?php

namespace Hoo\WordPressPluginFramework\Http\Decoders;

use Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeFactoryInterface;

readonly class Decoders implements DecodersInterface
{
	public function __construct(
		protected MediaTypeFactoryInterface $mediaTypeFactory,
		protected array $decoders,
	) {
	}

	public function get(string $contentType, mixed $encoded): DecoderInterface
	{
		$mediaType = $this->mediaTypeFactory->create($contentType);

		foreach ($this->decoders as $decoder) {
			if (
				$decoder->decodesMediaType($mediaType) &&
				$decoder->decodes($encoded)
			) {
				return $decoder;
			}
		}

		throw new DecodersException('no decoder found');
	}
}
