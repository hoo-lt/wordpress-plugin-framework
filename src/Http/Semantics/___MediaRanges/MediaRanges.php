<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\MediaRanges;

use ArrayIterator;
use Hoo\WordPressPluginFramework\Http\Semantics\MediaRanges\MediaRange\MediaRangeInterface;
use Traversable;

readonly class MediaRanges implements MediaRangesInterface
{
	/**
	 * @param list<MediaRangeInterface> $mediaRanges
	 */
	public function __construct(
		protected array $mediaRanges,
	) {
	}

	public function getIterator(): Traversable
	{
		return new ArrayIterator($this->mediaRanges);
	}

	public function count(): int
	{
		return count($this->mediaRanges);
	}
}
