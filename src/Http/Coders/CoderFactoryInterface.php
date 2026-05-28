<?php

namespace Hoo\WordPressPluginFramework\Http\Coders;

interface CoderFactoryInterface
{
	public function from(string $mediaType): CoderInterface;
	public function tryFrom(string $mediaType): ?CoderInterface;
}