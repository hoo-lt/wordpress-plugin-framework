<?php

namespace Hoo\WordPressPluginFramework\Http\Response;

use Hoo\WordPressPluginFramework\{
	Http\Headers\HeadersFactoryInterface,
	Http\Body\BodyFactoryInterface,
};

readonly class ResponseFactory implements ResponseFactoryInterface
{
	public function __construct(
		protected HeadersFactoryInterface $headersFactory,
		protected BodyFactoryInterface $bodyFactory,
	) {
	}

	public function from(int $statusCode, ?array $headers = null, array|string|null $body = null): ResponseInterface
	{
		$headers = $this->headersFactory->tryFrom($headers);
		$body = $this->bodyFactory->tryFrom(
			$body,
			$headers->contentType(),
		);

		return new Response($statusCode, $headers, $body);
	}
}