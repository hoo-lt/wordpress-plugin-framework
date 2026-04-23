<?php

namespace Hoo\WordPressPluginFramework\Http\Client\Response;

use Hoo\WordPressPluginFramework\Http\Headers\HeadersFactoryInterface;
use Hoo\WordPressPluginFramework\Http\Headers\HeadersInterface;

readonly class ResponseFactory implements ResponseFactoryInterface
{
	public function __construct(
		protected HeadersFactoryInterface $headersFactory,
	) {
	}

	public function from(HeadersInterface $headers, ?string $body, int $statusCode): ResponseInterface
	{
		return new Response($headers, $body, $statusCode);
	}

	public function fromArray(array $headers, ?string $body, int $statusCode): ResponseInterface
	{
		return $this->from($this->headersFactory->from($headers), $body, $statusCode);
	}
}