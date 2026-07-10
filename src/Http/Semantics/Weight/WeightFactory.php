<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Weight;

use Hoo\WordPressPluginFramework\Http\Semantics\Semantics;

readonly class WeightFactory implements WeightFactoryInterface
{
	// weight anchored to the tail of a media-range (RFC 9110 §12.4.2); the element arrives OWS-free from the splitter
	public const WEIGHT = '/' . Semantics::WEIGHT . '\z/';

	public function create(string $mediaRange): ?WeightInterface
	{
		if (preg_match(self::WEIGHT, $mediaRange, $matched) !== 1) {
			return null;   // no weight on the wire
		}

		return new Weight((float) $matched['qvalue']);
	}
}
