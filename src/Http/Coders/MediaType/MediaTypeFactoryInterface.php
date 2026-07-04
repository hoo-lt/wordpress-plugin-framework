<?php

namespace Hoo\WordPressPluginFramework\Http\Coders\MediaType;

interface MediaTypeFactoryInterface
{
	public function create(string $mediaType): MediaTypeInterface;
	public function tryCreate(?string $mediaType): ?MediaTypeInterface;
}
