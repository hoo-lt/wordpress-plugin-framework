<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType;

use Hoo\WordPressPluginFramework\Http\Abnf\Rfc9110;

readonly class MediaTypeFactory implements MediaTypeFactoryInterface
{
	public function create(string $mediaType): MediaTypeInterface
	{
		if (!preg_match('@\A' . Rfc9110::MEDIA_TYPE . '\z@', $mediaType, $match)) {
			throw new MediaTypeException('invalid media type');
		}

		return new MediaType($match['type'], $match['subtype']);
	}
}
