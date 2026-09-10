<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body;

interface BodyFactoryInterface
{
	public function createBody(string $contentType, mixed $body): BodyInterface;
	public function createBodies(mixed $body): array;

	public function createBodyFromEncoded(string $contentType, string $body): BodyInterface;

	public function createBodyFromUnnormalized(string $contentType, mixed $body): BodyInterface;
	public function createBodiesFromUnnormalized(mixed $body): array;
}
