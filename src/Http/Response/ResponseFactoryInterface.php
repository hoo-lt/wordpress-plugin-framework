<?php

namespace Hoo\WordPressPluginFramework\Http\Response;

use Hoo\WordPressPluginFramework\Http;
use Throwable;

interface ResponseFactoryInterface
{
	public function from(int $statusCode, ?array $headers = null, array|string|null $body = null): ResponseInterface;
}
