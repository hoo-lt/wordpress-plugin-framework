<?php

namespace Hoo\WordPressPluginFramework\Http\Client\Response;

use Hoo\WordPressPluginFramework\{
	Http\Message\Headers\HeadersFactoryInterface,
	Http\Message\Body\BodyFactoryInterface,
};

readonly class ResponseFactory implements ResponseFactoryInterface
{
	public function __construct(
		protected HeadersFactoryInterface $headersFactory,
		protected BodyFactoryInterface $bodyFactory,
	) {
	}

	public function create(int $statusCode, ?array $headers = null, ?string $body = null): ResponseInterface
	{
		$headers = $this->headersFactory->tryCreate($headers);
		$body = $this->bodyFactory->tryCreateFromEncoded(
			$body,
			$headers?->contentType(),
		);

		return new Response($statusCode, $headers, $body);
	}
}