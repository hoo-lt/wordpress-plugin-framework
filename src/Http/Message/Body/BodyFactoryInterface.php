<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body;

use Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;

interface BodyFactoryInterface
{
	public function createBody(MediaTypeInterface|string $contentType, mixed $body): BodyInterface;
	public function createBodies(mixed $body): array;

	public function createBodyFromEncoded(string $contentType, string $body): BodyInterface;

	public function createBodyFromUnnormalized(MediaTypeInterface|string $contentType, mixed $body): BodyInterface;
	public function createBodiesFromUnnormalized(mixed $body): array;
}
