<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType;

use Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;

interface ContentTypeInterface
{
	public function mediaType(): MediaTypeInterface;
}
