<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType;

interface MediaTypeInterface
{
	public function type(): string;
	public function subtype(): string;
}
