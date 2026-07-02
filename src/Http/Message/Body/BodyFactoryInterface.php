<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body;

interface BodyFactoryInterface
{
	public function createFromDecoded(object|array|string|float|int|bool $body, ?string $contentType = null): BodyInterface;
	public function tryCreateFromDecoded(object|array|string|float|int|bool|null $body, ?string $contentType = null): ?BodyInterface;

	public function createFromEncoded(string $body, ?string $contentType = null): BodyInterface;
	public function tryCreateFromEncoded(?string $body, ?string $contentType = null): ?BodyInterface;
}
