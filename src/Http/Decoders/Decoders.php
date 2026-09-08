<?php

namespace Hoo\WordPressPluginFramework\Http\Decoders;

use Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\ContentTypeFactoryInterface;

readonly class Decoders implements DecodersInterface
{
	public function __construct(
		protected ContentTypeFactoryInterface $contentTypeFactory,
		protected array $decoders,
	) {
	}

	public function get(string $contentType, mixed $encoded): DecoderInterface
	{
		$mediaType = $this->contentTypeFactory->create($contentType)->mediaType();

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
