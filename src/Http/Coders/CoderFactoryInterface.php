<?php

namespace Hoo\WordPressPluginFramework\Http\Coders;

use Hoo\WordPressPluginFramework\Http\Semantics\ContentType\MediaType\MediaTypeInterface;

interface CoderFactoryInterface
{
	public function createDecoder(MediaTypeInterface $mediaType): CoderInterface;
	public function createEncoder(mixed $decoded, MediaTypeInterface $mediaType): CoderInterface;
}
