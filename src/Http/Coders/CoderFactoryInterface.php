<?php

namespace Hoo\WordPressPluginFramework\Http\Coders;

interface CoderFactoryInterface
{
	public function createDecoder(string $mediaType, mixed $encoded): CoderInterface;
	public function tryCreateDecoder(string $mediaType, mixed $encoded): ?CoderInterface;

	public function createEncoder(string $mediaType, mixed $decoded): CoderInterface;
	public function tryCreateEncoder(string $mediaType, mixed $decoded): ?CoderInterface;
}
