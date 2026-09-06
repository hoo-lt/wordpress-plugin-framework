<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body;

interface BodyFactoryInterface
{
	public function create(string $contentType, mixed $body): BodyInterface;
	public function createFromEncoded(string $contentType, mixed $body): BodyInterface;
	public function createFromUnnormalized(string $contentType, mixed $body): BodyInterface;
}
