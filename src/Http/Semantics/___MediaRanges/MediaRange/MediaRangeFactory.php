<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\MediaRanges\MediaRange;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\Parameter\ParameterFactoryInterface,
	Http\Semantics\Semantics,
};

readonly class MediaRangeFactory implements MediaRangeFactoryInterface
{
	public function __construct(
		protected ParameterFactoryInterface $parameterFactory,
	) {
	}

	public function create(string $mediaRange): MediaRangeInterface
	{
		preg_match_all('/\A' . Semantics::TYPE . '\/' . Semantics::SUBTYPE . '|' . Semantics::WEIGHT . '(?=' . Semantics::OWS . ';|\z)|' . Semantics::PARAMETERS . '/', $mediaRange, $matches, PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL);

		foreach ($matches as $match) {
			if ($match['type'] !== null) {
				$type ??= strtolower($match['type']);
			}

			if ($match['subtype'] !== null) {
				$subtype ??= strtolower($match['subtype']);
			}

			if ($match['q'] !== null) {
				$q ??= $match['q'];
			}

			if ($match['parameter'] !== null) {
				$parameters[] = $this->parameterFactory->create($match['parameter']);
			}
		}

		return new MediaRange(
			$type ?? '',
			$subtype ?? '',
			$parameters ?? [],
			$q ?? 1.0
		);
	}

	public function tryCreate(?string $mediaRange): ?MediaRangeInterface
	{
		return $mediaRange === null ? null : $this->create($mediaRange);
	}
}
