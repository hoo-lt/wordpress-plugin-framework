<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers;

use Hoo\WordPressPluginFramework\{
	Http\Message\Headers\Accept\AcceptFactoryInterface,
	Http\Message\Headers\ContentType\ContentTypeFactoryInterface,
};

readonly class HeadersFactory implements HeadersFactoryInterface
{
	public function __construct(
		protected AcceptFactoryInterface $acceptFactory,
		protected ContentTypeFactoryInterface $contentTypeFactory,
	) {
	}

	public function create(array $headers): HeadersInterface
	{
		$headers = array_change_key_case($headers, CASE_LOWER);

		$accept = isset($headers['accept']) ? $this->acceptFactory->create($headers['accept']) : null;
		$contentType = isset($headers['content-type']) ? $this->contentTypeFactory->create($headers['content-type']) : null;

		return new Headers($headers, $accept, $contentType);
	}
}
