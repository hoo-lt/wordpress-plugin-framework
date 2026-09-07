<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers\Accept\MediaRange;

interface MediaRangeFactoryInterface
{
	public function create(string $mediaRange): MediaRangeInterface;
}
