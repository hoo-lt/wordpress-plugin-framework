<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body;

use Hoo\WordPressPluginFramework\{
	Helpers\KeyValue\HelperInterface,
	Http\Coders\CoderFactoryInterface,
	Http\Message\Body\Normalizer\NormalizerInterface,
	Http\Semantics\ContentType\MediaType\MediaTypeFactoryInterface,
};

readonly class BodyFactory implements BodyFactoryInterface
{
	public function __construct(
		protected HelperInterface $helper,
		protected CoderFactoryInterface $coderFactory,
		protected NormalizerInterface $normalizer,
		protected MediaTypeFactoryInterface $mediaTypeFactory,
	) {
	}

	public function createFromDecoded(mixed $body, string $contentType): BodyInterface
	{
		$mediaType = $this->mediaTypeFactory->create($contentType);
		$encoder = $this->coderFactory->createEncoder($body, $mediaType);

		$body = $this->normalizer->normalize($body);
		if (
			is_array($body) ||
			is_object($body)
		) {
			return new KeyValue\Body($this->helper, $encoder, $body);
		}

		return new Body($encoder, $body);
	}

	public function createFromEncoded(string $body, string $contentType): BodyInterface
	{
		$mediaType = $this->mediaTypeFactory->create($contentType);
		$decoder = $this->coderFactory->createDecoder($mediaType);

		$body = $decoder->decode($body);
		if (
			is_array($body) ||
			is_object($body)
		) {
			return new KeyValue\Body($this->helper, $decoder, $body);
		}

		return new Body($decoder, $body);
	}
}
