<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType;

interface MediaTypeFactoryInterface
{
	public function create(string $contentType): MediaTypeInterface;
}
