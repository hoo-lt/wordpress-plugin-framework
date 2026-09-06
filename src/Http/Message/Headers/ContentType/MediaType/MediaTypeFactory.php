<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Headers\ContentType\MediaType;

use Hoo\WordPressPluginFramework\{
	Http\Message\Headers\Parameters\ParametersFactoryInterface,
	Http\Abnf\Rfc9110,
};

readonly class MediaTypeFactory implements MediaTypeFactoryInterface
{
	public function create(string $mediaType): MediaTypeInterface
	{
		preg_match_all('/\A' . Rfc9110::TYPE . '\/' . Rfc9110::SUBTYPE . '|' . Rfc9110::PARAMETERS . '/', $mediaType, $matches, PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL);

		$type = '';
		$subtype = '';

		foreach ($matches as $match) {
			if ($match['type'] !== null) {
				$type = $match['type'];
			}

			if ($match['subtype'] !== null) {
				$subtype = $match['subtype'];
			}
		}

		if (
			$type === '' ||
			$subtype === '' ||
			$type === '*' ||
			$subtype === '*'
		) {
			throw new MediaTypeException('invalid media type');
		}

		return new MediaType($type, $subtype);
	}
}
