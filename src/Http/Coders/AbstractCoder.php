<?php

namespace Hoo\WordPressPluginFramework\Http\Coders;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\MediaType\MediaTypeInterface,
	Http\Semantics\MediaType\MediaTypeFactoryInterface,
};

abstract readonly class AbstractCoder implements CoderInterface
{
	public function __construct(
		protected MediaTypeFactoryInterface $mediaTypeFactory,
	) {
	}

	public function codes(MediaTypeInterface $mediaType): bool
	{
		$mediaTypes = $this->mediaTypes();
		foreach ($mediaTypes as $mt) {
			if (
				$mt->type() === $mediaType->type() &&
				$mt->subtype() === $mediaType->subtype()
			) {
				return true;
			}
		}

		return false;
	}
}