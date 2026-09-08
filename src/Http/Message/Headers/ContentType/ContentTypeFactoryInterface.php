<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType;

interface ContentTypeFactoryInterface
{
	public function create(string $contentType): ContentTypeInterface;
}
