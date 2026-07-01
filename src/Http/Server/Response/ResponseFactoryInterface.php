<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Response;

interface ResponseFactoryInterface
{
	public function create(int $statusCode, ?array $headers = null, mixed $body = null): ResponseInterface;
}
