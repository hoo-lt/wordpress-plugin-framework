<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body;

interface BodyFactoryInterface
{
	public function create(array|object|string $body, ?string $contentType = null): BodyInterface;
	public function tryCreate(array|object|string|null $body, ?string $contentType = null): ?BodyInterface;
}
