<?php

namespace Hoo\WordPressPluginFramework\Http\Body;

use Hoo\WordPressPluginFramework\Http;
use Throwable;

interface BodyFactoryInterface
{
	public function from(?string $contentType, string $body): BodyInterface;
	public function fromServer(): ?BodyInterface;

	public function fromException(Http\Request\RequestInterface $request, Http\Exceptions\Exception $exception): ?BodyInterface;

	public function fromThrowable(Http\Request\RequestInterface $request, Throwable $throwable): ?BodyInterface;
}
