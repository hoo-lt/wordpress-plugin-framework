<?php

namespace Hoo\WordPressPluginFramework\Http\Body;

interface BodyFactoryInterface
{
	public function from(array|string $body, ?string $contentType = null): BodyInterface;
	public function tryFrom(array|string|null $body, ?string $contentType = null): ?BodyInterface;
}
