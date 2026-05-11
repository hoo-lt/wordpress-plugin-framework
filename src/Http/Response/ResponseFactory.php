<?php

namespace Hoo\WordPressPluginFramework\Http\Response;

use Hoo\WordPressPluginFramework\Http;

readonly class ResponseFactory implements ResponseFactoryInterface
{
	public function __construct(
		protected Http\Headers\HeadersFactoryInterface $headersFactory,
		protected Http\Body\BodyFactoryInterface $bodyFactory,
	) {
	}

	public function from(int $statusCode, ?array $headers = null, ?string $body = null): ResponseInterface
	{
		$headers = $headers ? $this->headersFactory->from($headers) : null;
		$body = $body ? $this->bodyFactory->from(
			$headers->contentType(),
			$body
		) : null;

		return new Response(
			$statusCode,
			$headers,
			$body,
		);
	}
}