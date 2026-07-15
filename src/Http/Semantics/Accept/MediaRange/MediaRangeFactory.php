<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Accept\MediaRange;

use Hoo\WordPressPluginFramework\Http\Semantics\Semantics;

readonly class MediaRangeFactory implements MediaRangeFactoryInterface
{
	public function create(string $mediaRange): MediaRangeInterface
	{
		preg_match_all('/\A' . Semantics::TYPE . '\/' . Semantics::SUBTYPE . '|' . Semantics::WEIGHT . '(?=' . Semantics::OWS . ';|\z)|' . Semantics::PARAMETERS . '/', $mediaRange, $matches, PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL);

		$parameters = [];
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
				$parameters[strtolower($match['name'])] ??= $match['quoted_string'] !== null
					? $this->unquote($match['quoted_string'])
					: $match['token'];
			}
		}

		return new MediaRange(
			$type ?? '',
			$subtype ?? '',
			$parameters,
			$q ?? 1.0,
		);
	}

	public function tryCreate(?string $mediaRange): ?MediaRangeInterface
	{
		return $mediaRange === null ? null : $this->create($mediaRange);
	}

	private function unquote(string $quotedString): string
	{
		return preg_replace('/\A' . Semantics::DQUOTE . '|\x5C(.)|' . Semantics::DQUOTE . '\z/s', '$1', $quotedString);
	}
}
