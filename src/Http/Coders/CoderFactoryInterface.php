<?php

namespace Hoo\WordPressPluginFramework\Http\Coders;

interface CoderFactoryInterface
{
	public function coder(string $mediaType): ?CoderInterface;
}