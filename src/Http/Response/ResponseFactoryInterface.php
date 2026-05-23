<?php

namespace Hoo\WordPressPluginFramework\Http\Response;

use Hoo\WordPressPluginFramework\Http;
use Throwable;

interface ResponseFactoryInterface
{
	public function from(int $statusCode, ?array $headers = null, mixed $body = null): ResponseInterface;

	public function fromException(Http\Exceptions\Exception $exception): ResponseInterface;
	public function fromThrowable(Throwable $throwable): ResponseInterface;
}
