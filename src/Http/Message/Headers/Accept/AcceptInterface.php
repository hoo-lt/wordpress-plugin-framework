<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers\Accept;

use Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType\MediaTypeInterface;
use Stringable;

interface AcceptInterface extends Stringable
{
	public function mediaRanges(): array;

	public function q(MediaTypeInterface $mediaType): float;
}
