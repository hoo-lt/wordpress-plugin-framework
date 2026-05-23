<?php

namespace Hoo\WordPressPluginFramework\Http\Headers;

interface HeadersFactoryInterface
{
	public function from(array $headers): HeadersInterface;
	public function fromServer(): ?HeadersInterface;

	public function fromException(Http\Exceptions\Exception $exception): ?HeadersInterface;
	public function fromThrowable(Throwable $throwable): ?HeadersInterface;
}
