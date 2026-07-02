<?php

namespace Hoo\WordPressPluginFramework\Http\Coders;

abstract readonly class AbstractCoder implements CoderInterface
{
	public function codes(string $mediaType): bool
	{
		$mediaTypes = $this->mediaTypes();
		return in_array(strtolower($mediaType), $mediaTypes, true);
	}
}