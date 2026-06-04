<?php

namespace Hoo\WordPressPluginFramework\Http\Response;

interface ResponseFactoryInterface
{
	public function from(int $statusCode, ?array $headers = null, array|string|null $body = null): ResponseInterface;
}
