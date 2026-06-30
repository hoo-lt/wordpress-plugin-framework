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

	public function create(array|object|string $body, ?string $contentType = null): BodyInterface
	{
		if ($contentType === null) {
			if (
				is_array($body) ||
				is_object($body)
			) {
				throw new BodyFactoryException('cant use array or object w/o media type');
			}

			return new Body($body);
		}

		$coder = $this->coderFactory->tryCreate($contentType);
		if ($coder === null) {
			if (
				is_array($body) ||
				is_object($body)
			) {
				throw new BodyFactoryException('cant use array or object w/o coder');
			}

			return new Body($body);
		}

		if (is_string($body)) {
			$body = $coder->decode($body);
		}

		$body = $this->normalizer->normalize($body);

		return new KeyValue\Body($this->helper, $coder, $body);
	}

	public function tryCreate(array|object|string|null $body, ?string $contentType = null): ?BodyInterface
	{
		if ($body === null) {
			return null;
		}

		return $this->create($body, $contentType);
	}
}
