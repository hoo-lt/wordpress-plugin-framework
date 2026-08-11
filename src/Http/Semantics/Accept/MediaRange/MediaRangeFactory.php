<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Accept\MediaRange;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\Parameters\ParametersFactoryInterface,
	Http\Semantics\Semantics,
};

readonly class MediaRangeFactory implements MediaRangeFactoryInterface
{
	public function __construct(
		protected ParametersFactoryInterface $parametersFactory,
	) {
	}

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

		preg_match_all('/\A' . Semantics::TYPE . '\/' . Semantics::SUBTYPE . '|' . Semantics::WEIGHT . '|' . Semantics::PARAMETERS . '/', $mediaRange, $matches, PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL);

		$type = '';
		$subtype = '';
		$parameters = '';
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

			if ($match['parameters'] !== null) {
				$parameters .= $match['parameters'];
			}
		}

		if (
			$type === '' ||
			$subtype === ''
		) {
			return null;
		}

		return new MediaRange($type, $subtype, $this->parametersFactory->create($parameters), $q);
	}
}
