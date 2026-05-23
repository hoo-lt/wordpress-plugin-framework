<?php

namespace Hoo\WordPressPluginFramework\Http\Headers;

use Hoo\WordPressPluginFramework\Http;
use Throwable;

interface HeadersFactoryInterface
{
	public function from(array $headers): HeadersInterface;
	public function fromServer(): ?HeadersInterface;

	public function fromException(Http\Request\RequestInterface $request, Http\Exceptions\Exception $exception): ?HeadersInterface;
	public function fromThrowable(Http\Request\RequestInterface $request, Throwable $throwable): ?HeadersInterface;
}
