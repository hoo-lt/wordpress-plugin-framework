<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body;

interface BodyFactoryInterface
{
	public function createDecoded(string $encoded, ?string $contentType = null): BodyInterface;
	public function tryCreateDecoded(?string $encoded, ?string $contentType = null): ?BodyInterface;

	public function createEncoded(mixed $decoded, ?string $contentType = null): BodyInterface;
	public function tryCreateEncoded(mixed $decoded, ?string $contentType = null): ?BodyInterface;
}
