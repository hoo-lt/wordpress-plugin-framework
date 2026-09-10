<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType;

use Hoo\WordPressPluginFramework\{
	Http\Message\Headers\ContentType\MediaType\MediaType,
	Http\Message\Headers\Parameters\ParametersFactoryInterface,
	Http\Abnf\Rfc9110,
};

readonly class ContentTypeFactory implements ContentTypeFactoryInterface
{
	public function __construct(
		protected ParametersFactoryInterface $parametersFactory,
	) {
	}

	public function create(string $contentType): ContentTypeInterface
	{
		if (preg_match('@\A' . Rfc9110::CONTENT_TYPE . '\z@', $contentType, $match) !== 1) {
			throw new ContentTypeFactoryException('invalid content type');
		}

		$parameters = $this->parametersFactory->create($match['parameters']);
		$mediaType = new MediaType($match['type'], $match['subtype'], $parameters);

		return new ContentType($mediaType);
	}
}
