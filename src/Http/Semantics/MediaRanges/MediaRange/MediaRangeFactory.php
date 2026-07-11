<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\MediaRanges\MediaRange;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\Parameter\ParameterFactoryInterface,
	Http\Semantics\Semantics,
};

readonly class MediaRangeFactory implements MediaRangeFactoryInterface
{
	// One pass tokenizes the element into classified facets (RFC 9110 §12.5.1):
	//   essence   — type "/" subtype, only at the very start;
	//   weight    — a q parameter wherever it stands ("Recipients SHOULD process any parameter
	//               named "q" as weight, regardless of parameter ordering");
	//   parameter — any other media type parameter, captured bare — its framing consumed by the
	//               scan, its quoted value consumed whole, so the scan can never resume inside
	//               quoted data.
	// Weight precedes parameter in the alternation: a valid q is never a media parameter
	// (the media type registry disallows parameters named "q").
	// The lookahead pins the qvalue to an element boundary — OWS ";" (the next parameter's
	// framing, §5.6.6) or bare "\z": an element can never end in OWS, since list framing
	// (§5.6.1) and field framing (§5.5) are excluded upstream.
	protected const MEDIA_RANGE = '/'
		. '\A' . Semantics::TYPE . '\/' . Semantics::SUBTYPE
		. '|' . Semantics::WEIGHT . '(?=' . Semantics::OWS . ';|\z)'
		. '|' . Semantics::PARAMETERS
		. '/';

	public function __construct(
		protected ParameterFactoryInterface $parameterFactory,
	) {
	}

	public function create(string $mediaRange): MediaRangeInterface
	{
		preg_match_all(self::MEDIA_RANGE, $mediaRange, $facets, PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL);

		$type = '';
		$subtype = '';
		$weight = null;
		$parameters = [];

		foreach ($facets as $facet) {
			if ($facet['type'] !== null) {              // essence — at most once (\A-anchored)
				$type = $facet['type'];
				$subtype = $facet['subtype'];
			} elseif ($facet['qvalue'] !== null) {      // weight — the first valid q wins
				$weight ??= (float) $facet['qvalue'];   // ??= is null-strict: an explicit q=0 stays 0.0
			} else {
				$parameters[] = $this->parameterFactory->create($facet['parameter']);
			}
		}

		return new MediaRange(strtolower($type), strtolower($subtype), $parameters, $weight ?? 1.0);   // absent weight decodes to 1 (§12.4.2)
	}

	public function tryCreate(?string $mediaRange): ?MediaRangeInterface
	{
		return $mediaRange === null ? null : $this->create($mediaRange);
	}
}
