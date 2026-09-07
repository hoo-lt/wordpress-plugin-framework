<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers\Accept\MediaRange;

use Hoo\WordPressPluginFramework\Http\Abnf\Rfc9110;

readonly class MediaRangeFactory implements MediaRangeFactoryInterface
{
	public function create(string $mediaRange): MediaRangeInterface
	{
		if (!preg_match('@\A' . Rfc9110::MEDIA_RANGE . '\z@', $mediaRange, $match)) {
			throw new MediaRangeException('invalid media range');
		}

		return new MediaRange($match['type'], $match['subtype'], $this->q($mediaRange));
	}

	protected function q(string $mediaRange): string
	{
		preg_match_all('@' . Rfc9110::PARAMETER . '@', $mediaRange, $parameters, PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL); //replace with weight?

		foreach ($parameters as $parameter) {
			if (strcasecmp($parameter['parameter_name'], 'q') !== 0) {
				continue;
			}

			return $parameter['token'] ?? $parameter['quoted_string'];
		}

		return '1';
	}
}
