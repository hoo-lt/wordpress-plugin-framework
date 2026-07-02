<?php

namespace Hoo\WordPressPluginFramework\Http\Coders;

interface CoderFactoryInterface
{
	public function createDecoder(mixed $encoded, string $mediaType): CoderInterface;
	public function tryCreateDecoder(mixed $encoded, string $mediaType): ?CoderInterface;

	public function createEncoder(mixed $decoded, string $mediaType): CoderInterface;
	public function tryCreateEncoder(mixed $decoded, string $mediaType): ?CoderInterface;
}
