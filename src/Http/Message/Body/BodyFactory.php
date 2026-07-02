<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body;

use Hoo\WordPressPluginFramework\{
	Helpers\KeyValue\HelperInterface,
	Http\Coders\CoderFactoryInterface,
	Http\Message\Body\Normalizer\NormalizerInterface,
};

readonly class BodyFactory implements BodyFactoryInterface
{
	public function __construct(
		protected HelperInterface $helper,
		protected CoderFactoryInterface $coderFactory,
		protected NormalizerInterface $normalizer,
	) {
	}

	public function createFromDecoded(object|array|string|float|int|bool $body, ?string $contentType = null): BodyInterface
	{
		$body = $this->normalizer->normalize($body);

		if ($contentType === null) {
			throw new BodyFactoryException('the content type is required');
		}

		$encoder = $this->coderFactory->tryCreateEncoder($body, $contentType);
		if ($encoder === null) {
			throw new BodyFactoryException("no coder encodes {$contentType} content type");
		}

		if (is_array($body)) {
			return new KeyValue\Body($this->helper, $encoder, $body);
		}

		return new Body($encoder, $body);
	}

	public function tryCreateFromDecoded(object|array|string|float|int|bool|null $body, ?string $contentType = null): ?BodyInterface
	{
		if ($body === null) {
			return null;
		}

		return $this->createFromDecoded($body, $contentType);
	}

	public function createFromEncoded(string $body, ?string $contentType = null): BodyInterface
	{
		if ($contentType === null) {
			throw new BodyFactoryException('the content type is required');
		}

		$decoder = $this->coderFactory->tryCreateDecoder($body, $contentType);
		if ($decoder === null) {
			throw new BodyFactoryException("no coder decodes {$contentType} content type");
		}

		$body = $decoder->decode($body);
		if (is_array($body)) {
			return new KeyValue\Body($this->helper, $decoder, $body);
		}

		return new Body($decoder, $body);
	}

	public function tryCreateFromEncoded(?string $body, ?string $contentType = null): ?BodyInterface
	{
		if ($body === null) {
			return null;
		}

		return $this->createFromEncoded($body, $contentType);
	}
}
