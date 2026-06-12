<?php

namespace Hoo\WordPressPluginFramework\Http\Coders;

interface CoderFactoryInterface
{
	public function create(string $mediaType): CoderInterface;
	public function tryCreate(string $mediaType): ?CoderInterface;
}