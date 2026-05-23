<?php

namespace Hoo\WordPressPluginFramework\Http\Body;

use Hoo\WordPressPluginFramework\Http;
use Throwable;

interface BodyFactoryInterface
{
	public function from(?string $contentType, string $body): BodyInterface;
	public function fromServer(): ?BodyInterface;

	public function fromException(?string $contentType, Http\Exceptions\Exception $exception): ?BodyInterface;

	public function fromThrowable(?string $contentType, Throwable $throwable): ?BodyInterface;
}
