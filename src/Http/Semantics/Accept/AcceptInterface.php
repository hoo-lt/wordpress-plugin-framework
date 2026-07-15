<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Accept;

use Countable;
use Hoo\WordPressPluginFramework\{
	Http\Semantics\Accept\MediaRangeWeight\Weight\WeightInterface,
	Http\Semantics\ContentType\MediaType\MediaTypeInterface,
};

interface AcceptInterface extends Countable
{
	public function mediaRanges(): array;
	public function mediaTypes(): array;

	public function q(MediaTypeInterface $mediaType): ?float;
}
