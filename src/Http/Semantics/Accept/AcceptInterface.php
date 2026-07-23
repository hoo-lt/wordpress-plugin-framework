<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Accept;

use Hoo\WordPressPluginFramework\Http\Semantics\ContentType\MediaType\MediaTypeInterface;

interface AcceptInterface
{
	public function mediaRanges(): array;
	public function mediaTypes(): array;

	public function q(MediaTypeInterface $mediaType): float;
}
