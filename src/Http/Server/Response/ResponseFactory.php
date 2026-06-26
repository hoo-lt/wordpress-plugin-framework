<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Response;

use Hoo\WordPressPluginFramework\{
	Http\Message\Headers\HeadersFactoryInterface,
	Http\Message\Body\BodyFactoryInterface,
	Uuid\UuidInterface,
};

readonly class ResponseFactory implements ResponseFactoryInterface
{
	public function __construct(
		protected UuidInterface $uuid,
		protected HeadersFactoryInterface $headersFactory,
		protected BodyFactoryInterface $bodyFactory,
	) {
	}

	public function create(int $statusCode, ?array $headers = null, array|string|null $body = null): ResponseInterface
	{
		$headers = $this->headersFactory->tryCreate($headers);
		$body = $this->bodyFactory->tryCreate(
			$body,
			$headers->contentType(),
		);

		return new Response($this->uuid, $statusCode, $headers, $body);
	}
}