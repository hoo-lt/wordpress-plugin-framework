<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType;

use Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;

readonly class ContentType implements ContentTypeInterface
{
	public function __construct(
		protected MediaTypeInterface $mediaType,
	) {
	}

	public function mediaType(): MediaTypeInterface
	{
		return $this->mediaType;
	}
}
