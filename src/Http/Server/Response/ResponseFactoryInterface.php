<?php

namespace Hoo\WordPressPluginFramework\Http\Server\Response;

interface ResponseFactoryInterface
{
	public function create(int $statusCode, array $headers = [], object|array|string|float|int|bool|null $body = null): ResponseInterface;
}
