<?php

namespace Hoo\WordPressPluginFramework\Http\Client\Response;

use Hoo\WordPressPluginFramework\Http\Headers\HeadersInterface;

interface ResponseFactoryInterface
{
	public function from(HeadersInterface $headers, ?string $body, int $statusCode): ResponseInterface;
	public function fromArray(array $headers, ?string $body, int $statusCode): ResponseInterface;
}
