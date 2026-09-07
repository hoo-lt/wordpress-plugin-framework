<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers\Accept;

use Hoo\WordPressPluginFramework\{
	Http\Message\Headers\Accept\MediaRange\MediaRangeFactoryInterface,
	Http\Abnf\Rfc9110,
};

readonly class AcceptFactory implements AcceptFactoryInterface
{
	public function __construct(
		protected MediaRangeFactoryInterface $mediaRangeFactory,
	) {
	}

	public function create(string $accept): AcceptInterface
	{
		preg_match_all('@' . Rfc9110::MEDIA_RANGE . '@', $accept, $matches, PREG_SET_ORDER);

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
