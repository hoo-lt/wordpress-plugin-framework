<?php

namespace Hoo\WordPressPluginFramework\Http\Encoders;

use Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\ContentTypeFactoryInterface;

readonly class Encoders implements EncodersInterface
{
	public function __construct(
		protected ContentTypeFactoryInterface $contentTypeFactory,
		protected array $encoders,
	) {
	}

	public function get(string $contentType, mixed $encoded): EncoderInterface
	{
		$mediaType = $this->contentTypeFactory->create($contentType)->mediaType();

		foreach ($this->encoders as $encoder) {
			if (
				$encoder->encodesMediaType($mediaType) &&
				$encoder->encodes($encoded)
			) {
				return $encoder;
			}
		}

		throw new EncodersException('no encoder found');
	}
}
