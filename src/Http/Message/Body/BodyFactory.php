<?php

namespace Hoo\WordPressPluginFramework\Http\Body;

use Hoo\WordPressPluginFramework\{
	Helpers\KeyValue\HelperInterface,
	Http\Coders\CoderFactoryInterface,
};

readonly class BodyFactory implements BodyFactoryInterface
{
	public function __construct(
		protected HelperInterface $helper,
		protected CoderFactoryInterface $coderFactory,
	) {
	}

	public function from(array|string $body, ?string $contentType = null): BodyInterface
	{
		if ($contentType === null) {
			if (is_array($body)) {
				throw new BodyFactoryException('cant use array w/o media type');
			}

			return new Body($body);
		}

		$coder = $this->coderFactory->tryFrom($contentType);
		if ($coder === null) {
			if (is_array($body)) {
				throw new BodyFactoryException('cant use array w/o coder');
			}

			return new Body($body);
		}

		if (is_string($body)) {
			$body = $coder->decode($body);
		}

		return new KeyValue\Body(
			$this->helper,
			$coder,
			$body,
		);
	}

	public function tryFrom(array|string|null $body, ?string $contentType = null): ?BodyInterface
	{
		if ($body === null) {
			return null;
		}

		return $this->from($body, $contentType);
	}
}
