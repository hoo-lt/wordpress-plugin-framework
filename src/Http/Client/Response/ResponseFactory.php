<?php

namespace Hoo\WordPressPluginFramework\Http\Client\Response;

use Hoo\WordPressPluginFramework\{
	Http\Message\Headers\HeadersFactoryInterface,
	Http\Message\Headers\HeadersInterface,
	Http\Message\Body\BodyFactoryInterface,
	Http\Message\Body\BodyInterface,
};

readonly class ResponseFactory implements ResponseFactoryInterface
{
	public function __construct(
		protected HeadersFactoryInterface $headersFactory,
		protected BodyFactoryInterface $bodyFactory,
	) {
	}

	public function create(int $statusCode, array $headers = [], ?string $body = null): ResponseInterface
	{
		$headers = $this->headers($headers);
		$body = $body === null ? null : $this->bodyFactory->createFromEncoded($body, $headers->contentType() ?? 'application/octet-stream');

		return new Response($statusCode, $headers, $body);
	}


	protected function headers(array $headers): HeadersInterface
	{
		return $this->headersFactory->create($headers);
	}

	protected function body(mixed $body, ?string $contentType): ?BodyInterface
	{
		if ($body === null) {
			return null;
		}

		if ($contentType === null) {
			$contentType = 'application/octet-stream';
		}

		return $this->bodyFactory->createFromEncoded($body, $contentType);
	}
}