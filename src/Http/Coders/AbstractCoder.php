<?php

namespace Hoo\WordPressPluginFramework\Http\Coders;

use Hoo\WordPressPluginFramework\Http\Semantics\ContentType\MediaType\MediaTypeInterface;

abstract readonly class AbstractCoder implements CoderInterface
{
	public function codes(MediaTypeInterface $mediaType): bool
	{
		// by essence only: coding capability is parameter-blind — precedence() would reject
		// e.g. Content-Type: application/json;charset=utf-8 against the bare canonical form
		return (bool) array_filter(
			$this->mediaTypes(),
			fn(MediaTypeInterface $mt): bool =>
				$mt->type() === $mediaType->type() &&
				$mt->subtype() === $mediaType->subtype(),
		);
	}
}
