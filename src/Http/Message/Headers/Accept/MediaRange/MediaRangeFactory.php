<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers\Accept\MediaRange;

use Hoo\WordPressPluginFramework\{
	Http\Message\Headers\Parameters\ParametersFactoryInterface,
	Http\Abnf\Rfc9110,
};

readonly class MediaRangeFactory implements MediaRangeFactoryInterface
{
	public function create(string $mediaRange): MediaRangeInterface
	{
		$mediaRange = $this->tryCreate($mediaRange);
		if ($mediaRange === null) {
			throw new MediaRangeException('invalid media range');
		}

		return $mediaRange;
	}

	public function tryCreate(?string $mediaRange): ?MediaRangeInterface
	{
		if ($mediaRange === null) {
			return null;
		}

		preg_match_all('/\A' . Rfc9110::TYPE . '\/' . Rfc9110::SUBTYPE . '|' . Rfc9110::WEIGHT . '|' . Rfc9110::PARAMETERS . '/', $mediaRange, $matches, PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL);

		$type = '';
		$subtype = '';
		$q = 1.000;

		foreach ($matches as $match) {
			if ($match['type'] !== null) {
				$type = $match['type'];
			}

			if ($match['subtype'] !== null) {
				$subtype = $match['subtype'];
			}

			if ($match['q'] !== null) {
				$q = $match['q'];
			}
		}

		if (
			$type === '' ||
			$subtype === ''
		) {
			return null;
		}

		return new MediaRange($type, $subtype, $q);
	}
}
