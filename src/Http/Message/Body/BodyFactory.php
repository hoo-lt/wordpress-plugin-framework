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

	public function createDecoded(string $encoded, ?string $contentType = null): BodyInterface
	{
		if ($contentType === null) {
			return new Body($encoded);
		}

		$coder = $this->coderFactory->tryCreateDecoder($contentType, $encoded);
		if ($coder === null) {
			return new Body($encoded);
		}

		$decoded = $coder->decode($encoded);

		return is_array($decoded)
			? new KeyValue\Body($this->helper, $coder, $decoded)
			: new Body($encoded);
	}

	public function tryCreateDecoded(?string $encoded, ?string $contentType = null): ?BodyInterface
	{
		if ($encoded === null) {
			return null;
		}

		return $this->createDecoded($encoded, $contentType);
	}

	public function createEncoded(mixed $decoded, ?string $contentType = null): BodyInterface
	{
		if (is_string($decoded)) {
			return new Body($decoded);
		}

		if ($contentType === null) {
			throw new BodyFactoryException('non-string body requires a media type');
		}

		$decoded = $this->normalizer->normalize($decoded);

		$coder = $this->coderFactory->tryCreateEncoder($contentType, $decoded);
		if ($coder === null) {
			throw new BodyFactoryException('no coder encodes this value for this media type');
		}

		return is_array($decoded)
			? new KeyValue\Body($this->helper, $coder, $decoded)
			: new Body($coder->encode($decoded));
	}

	public function tryCreateEncoded(mixed $decoded, ?string $contentType = null): ?BodyInterface
	{
		if ($decoded === null) {
			return null;
		}

		return $this->createEncoded($decoded, $contentType);
	}
}
