<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\MediaRanges;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\MediaRanges\MediaRange\MediaRangeFactoryInterface,
	Http\Semantics\Semantics,
};

readonly class MediaRangesFactory implements MediaRangesFactoryInterface
{
	// Accept = #( media-range [ weight ] ), RFC 9110 §5.6.1 + §12.5.1.
	// List framing ( OWS "," OWS ) and empty elements are consumed here; the captured <element>
	// is an exact media-range[weight] with no leading/trailing OWS. Trailing OWS is trimmed by
	// forcing the last captured octet to be a non-OWS char, handing whitespace back to the outer OWS.
	protected const ELEMENT = '/'
		. Semantics::OWS                                                                                       // leading list OWS — consumed, never captured
		. '(?<element>'
		.     '(?:' . Semantics::QUOTED_STRING . '|[^,' . Semantics::SP . Semantics::HTAB . '])'               // first octet: never OWS
		.     '(?:'
		.         '(?:' . Semantics::QUOTED_STRING . '|[^,])*'                                                  // interior: internal OWS + params; commas only inside quoted-string
		.         '(?:' . Semantics::QUOTED_STRING . '|[^,' . Semantics::SP . Semantics::HTAB . '])'            // last octet: never OWS → trailing OWS excluded
		.     ')?'
		. ')'
		. Semantics::OWS . '(?:,|\z)'                                                                          // trailing list OWS + delimiter — consumed, never captured
		. '/';

	public function __construct(
		protected MediaRangeFactoryInterface $mediaRangeFactory,
	) {
	}

	public function create(string $accept): MediaRangesInterface
	{
		preg_match_all(self::ELEMENT, $accept, $matches, PREG_SET_ORDER);

		$ranges = [];
		foreach ($matches as $match) {
			$ranges[] = $this->mediaRangeFactory->create($match['element']);
		}

		return new MediaRanges($ranges);
	}

	public function tryCreate(?string $accept): ?MediaRangesInterface
	{
		return $accept === null ? null : $this->create($accept);
	}
}
