<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\MediaRanges;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\MediaRanges\MediaRange\MediaRangeFactoryInterface,
	Http\Semantics\Semantics,
};

readonly class MediaRangesFactory implements MediaRangesFactoryInterface
{
	public function __construct(
		protected MediaRangeFactoryInterface $mediaRangeFactory,
	) {
	}

	public function create(string $mediaRanges): MediaRangesInterface
	{
		preg_match_all('/(?<media_range>(?>' . Semantics::QUOTED_STRING . '|[^,' . Semantics::SP . Semantics::HTAB . '])(?:(?>' . Semantics::QUOTED_STRING . '|[^,])*(?>' . Semantics::QUOTED_STRING . '|[^,' . Semantics::SP . Semantics::HTAB . ']))?)(?:' . Semantics::OWS . ',' . Semantics::OWS . '|\z)/', $mediaRanges, $matches, PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL);

		$ranges = [];
		foreach ($matches as $match) {
			$ranges[] = $this->mediaRangeFactory->create($match['media_range']);
		}

		return new MediaRanges($ranges);
	}

	public function tryCreate(?string $accept): ?MediaRangesInterface
	{
		return $accept === null ? null : $this->create($accept);
	}
}
