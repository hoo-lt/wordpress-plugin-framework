<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body;

interface BodyFactoryInterface
{
	public function createFromDecoded(mixed $body, string $contentType): BodyInterface;
	public function createFromEncoded(string $body, string $contentType): BodyInterface;
}
