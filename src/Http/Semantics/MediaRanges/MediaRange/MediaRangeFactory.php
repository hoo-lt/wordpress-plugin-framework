<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\MediaRanges\MediaRange;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\Parameters\ParametersFactoryInterface,
	Http\Semantics\Semantics,
};

readonly class MediaRangeFactory implements MediaRangeFactoryInterface
{
	// One pass tokenizes the element into classified facets (RFC 9110 §12.5.1):
	//   essence   — type "/" subtype, only at the very start;
	//   weight    — a q parameter wherever it stands ("Recipients SHOULD process any parameter
	//               named "q" as weight, regardless of parameter ordering");
	//   parameter — any other media type parameter, its quoted value consumed whole, so the
	//               scan can never resume inside quoted data.
	// Weight precedes parameter in the alternation: a valid q is never a media parameter
	// (the media type registry disallows parameters named "q").
	protected const MEDIA_RANGE = '/'
		. '\A' . Semantics::TYPE . '\/' . Semantics::SUBTYPE
		. '|' . Semantics::WEIGHT . '(?=' . Semantics::OWS . '(?:;|\z))'
		. '|(?<parameter>' . Semantics::OWS . ';' . Semantics::OWS . Semantics::PARAMETER . ')'
		. '/';

	public function __construct(
		protected ParametersFactoryInterface $parametersFactory,
	) {
	}

	public function create(string $mediaRange): MediaRangeInterface
	{
		preg_match_all(self::MEDIA_RANGE, $mediaRange, $matched);

		$parameters = implode($matched['parameter']);              // the media-parameter tokens, re-joined as a parameters wire
		$qvalue = current(array_diff($matched['qvalue'], ['']));   // first weight on the wire; '' marks the non-weight tokens

		return new MediaRange(
			strtolower(implode($matched['type'])),        // essence fires at most once (\A-anchored) — its column folds to it, or to empty-but-present
			strtolower(implode($matched['subtype'])),
			$this->parametersFactory->create($parameters),
			$qvalue === false ? null : (float) $qvalue,   // strict ===: no weight on the wire → null, never truthiness
		);
	}

	public function tryCreate(?string $mediaRange): ?MediaRangeInterface
	{
		return $mediaRange === null ? null : $this->create($mediaRange);
	}
}
