<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body;

interface BodyFactoryInterface
{
	public function createBody(string $contentType, mixed $body): BodyInterface;
	public function createBodyFromEncoded(string $contentType, mixed $body): BodyInterface;
	public function createBodyFromUnnormalized(string $contentType, mixed $body): BodyInterface;

	/**
	 * @return array<string, BodyInterface>
	 */
	public function createBodiesFromUnnormalized(mixed $body): array;
}
