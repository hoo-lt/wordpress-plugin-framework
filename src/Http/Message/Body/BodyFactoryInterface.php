<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body;

interface BodyFactoryInterface
{
	public function create(array|string $body, ?string $contentType = null): BodyInterface;
	public function tryCreate(array|string|null $body, ?string $contentType = null): ?BodyInterface;
}
