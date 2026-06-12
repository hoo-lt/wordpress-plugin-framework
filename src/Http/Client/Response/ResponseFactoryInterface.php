<?php

namespace Hoo\WordPressPluginFramework\Http\Client\Response;

interface ResponseFactoryInterface
{
	public function create(int $statusCode, ?array $headers = null, array|string|null $body = null): ResponseInterface;
}
