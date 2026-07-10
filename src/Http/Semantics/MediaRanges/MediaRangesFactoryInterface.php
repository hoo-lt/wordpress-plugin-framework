<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\MediaRanges;

interface MediaRangesFactoryInterface
{
	public function create(string $accept): MediaRangesInterface;
	public function tryCreate(?string $accept): ?MediaRangesInterface;
}
