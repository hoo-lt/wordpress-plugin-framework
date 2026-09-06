<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType;

use Stringable;
use Traversable;

interface MediaTypeInterface extends Stringable
{
	public function type(): string;
	public function subtype(): string;
}
