<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Accept;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\Accept\MediaRange\MediaRangeFactoryInterface,
	Http\Semantics\Semantics,
};

readonly class AcceptFactory implements AcceptFactoryInterface
{
	public function __construct(
		protected MediaRangeFactoryInterface $mediaRangeFactory,
	) {
	}

	public function create(string $accept): AcceptInterface
	{
		preg_match_all('/(?<media_range>(?>' . Semantics::QUOTED_STRING . '|[^,' . Semantics::WSP . '])(?:(?>' . Semantics::QUOTED_STRING . '|[^,])*(?>' . Semantics::QUOTED_STRING . '|[^,' . Semantics::WSP . ']))?)(?:' . Semantics::OWS . ',' . Semantics::OWS . '|\z)/', $accept, $matches, PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL);

		$mediaRanges = [];

		foreach ($matches as $match) {
			$mediaRange = $this->mediaRangeFactory->tryCreate($match['media_range']);
			if ($mediaRange === null) {
				continue;
			}

			$mediaRanges[] = $mediaRange;
		}

		return new Accept($mediaRanges);
	}

	public function tryCreate(?string $accept): ?AcceptInterface
	{
		if ($accept === null) {
			return null;
		}

		return $this->create($accept);
	}
}
