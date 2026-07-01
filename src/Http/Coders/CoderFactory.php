<?php

namespace Hoo\WordPressPluginFramework\Http\Coders;

readonly class CoderFactory implements CoderFactoryInterface
{
	public function __construct(
		protected array $coders,
	) {
	}

	public function createDecoder(string $mediaType, mixed $encoded): CoderInterface
	{
		$coder = $this->tryCreateDecoder($mediaType, $encoded);
		if ($coder === null) {
			throw new CoderFactoryException('no coder decodes this media type');
		}

		return $coder;
	}

	public function tryCreateDecoder(string $mediaType, mixed $encoded): ?CoderInterface
	{
		foreach ($this->coders as $coder) {
			if (
				$coder->supports($mediaType) &&
				$coder->decodes($encoded)
			) {
				return $coder;
			}
		}

		return null;
	}

	public function createEncoder(string $mediaType, mixed $decoded): CoderInterface
	{
		$coder = $this->tryCreateEncoder($mediaType, $decoded);
		if ($coder === null) {
			throw new CoderFactoryException('no coder encodes this value for this media type');
		}

		return $coder;
	}

	public function tryCreateEncoder(string $mediaType, mixed $decoded): ?CoderInterface
	{
		foreach ($this->coders as $coder) {
			if (
				$coder->supports($mediaType) &&
				$coder->encodes($decoded)
			) {
				return $coder;
			}
		}

		return null;
	}
}
