<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Response;

use Hoo\WordPressPluginFramework\{
	Http\Message\Headers\HeadersFactoryInterface,
	Http\Message\Body\BodyFactoryInterface,
	Uuid\UuidFactoryInterface,
};

readonly class ResponseFactory implements ResponseFactoryInterface
{
	public function __construct(
		protected UuidFactoryInterface $uuidFactory,
		protected HeadersFactoryInterface $headersFactory,
		protected BodyFactoryInterface $bodyFactory,
	) {
	}

	public function create(int $statusCode, ?array $headers = null, array|string|null $body = null): ResponseInterface
	{
		$uuid = $this->uuidFactory->create();
		$headers = $this->headersFactory->tryCreate($headers);
		$body = $this->bodyFactory->tryCreate(
			$body,
			$headers->contentType(),
		);

		return new Response($uuid, $statusCode, $headers, $body);
	}
}