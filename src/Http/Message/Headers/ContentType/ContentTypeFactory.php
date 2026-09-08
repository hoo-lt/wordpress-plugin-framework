<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType;

use Hoo\WordPressPluginFramework\{
	Http\Message\Headers\ContentType\MediaType\MediaType,
	Http\Abnf\Rfc9110,
};

readonly class ContentTypeFactory implements ContentTypeFactoryInterface
{
	public function create(string $contentType): ContentTypeInterface
	{
		if (preg_match('@\A' . Rfc9110::CONTENT_TYPE . '\z@', $contentType, $match) !== 1) {
			throw new ContentTypeFactoryException('invalid content type');
		}

		$mediaType = new MediaType($match['type'], $match['subtype']);

		return new ContentType($mediaType);
	}
}
