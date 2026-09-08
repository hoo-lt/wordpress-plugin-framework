<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers\Accept;

use Hoo\WordPressPluginFramework\{
	Http\Message\Headers\Accept\MediaRange\MediaRange,
	Http\Abnf\Rfc9110,
};

readonly class AcceptFactory implements AcceptFactoryInterface
{
	public function create(string $accept): AcceptInterface
	{
		if (preg_match('@\A' . Rfc9110::ACCEPT . '\z@J', $accept) !== 1) {
			throw new AcceptFactoryException('invalid accept');
		}

		preg_match_all('@' . Rfc9110::MEDIA_RANGE . Rfc9110::WEIGHT . '|' . Rfc9110::MEDIA_RANGE . '@J', $accept, $matches, PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL);

		$mediaRanges = [];

		foreach ($matches as $match) {
			$mediaRanges[] = new MediaRange($match['type'], $match['subtype'], $match['q'] ?? '1');
		}

		return new Accept($mediaRanges);
	}
}
