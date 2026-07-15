<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\ContentType\MediaType;

interface MediaTypeFactoryInterface
{
	public function create(string $mediaType): MediaTypeInterface;
	public function tryCreate(?string $mediaType): ?MediaTypeInterface;
}
