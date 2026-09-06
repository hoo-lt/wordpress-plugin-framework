<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers\Accept;

use Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;

interface AcceptInterface
{
	public function mediaRanges(): array;

	public function q(MediaTypeInterface $mediaType): float;
}
