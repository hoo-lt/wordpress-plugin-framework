<?php

namespace Hoo\WordPressPluginFramework\Http\Coders;

use Hoo\WordPressPluginFramework\Http\Semantics\ContentType\MediaType\MediaTypeInterface;

interface CoderFactoryInterface
{
	public function createDecoder(mixed $encoded, MediaTypeInterface $mediaType): CoderInterface;
	public function tryCreateDecoder(mixed $encoded, MediaTypeInterface $mediaType): ?CoderInterface;

	public function createEncoder(mixed $decoded, MediaTypeInterface $mediaType): CoderInterface;
	public function tryCreateEncoder(mixed $decoded, MediaTypeInterface $mediaType): ?CoderInterface;
}
