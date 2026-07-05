<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\MediaType;

interface MediaTypeFactoryInterface
{
	public function create(string $mediaType): MediaTypeInterface;
	public function tryCreate(?string $mediaType): ?MediaTypeInterface;
}
