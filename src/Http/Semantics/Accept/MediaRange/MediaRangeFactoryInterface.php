<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Accept\MediaRange;

interface MediaRangeFactoryInterface
{
	public function create(string $mediaRange): MediaRangeInterface;
	public function tryCreate(?string $mediaRange): ?MediaRangeInterface;
}
