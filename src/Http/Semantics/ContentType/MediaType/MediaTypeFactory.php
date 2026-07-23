<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\ContentType\MediaType;

use Hoo\WordPressPluginFramework\Http\Semantics\Semantics;

readonly class MediaTypeFactory implements MediaTypeFactoryInterface
{
	public function create(string $mediaType): MediaTypeInterface
	{
		$mediaType = $this->tryCreate($mediaType);
		if ($mediaType === null) {
			throw new MediaTypeException('invalid media type');
		}

		return $mediaType;
	}

	public function tryCreate(?string $mediaType): ?MediaTypeInterface
	{
		if ($mediaType === null) {
			return null;
		}

		preg_match_all('/\A' . Semantics::TYPE . '\/' . Semantics::SUBTYPE . '|' . Semantics::PARAMETERS . '/', $mediaType, $matches, PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL);

		$type = '';
		$subtype = '';
		$parameters = [];

		foreach ($matches as $match) {
			if ($match['type'] !== null) {
				$type = strtolower($match['type']);
			}

			if ($match['subtype'] !== null) {
				$subtype = strtolower($match['subtype']);
			}

			if ($match['parameter'] !== null) {
				$parameters[strtolower($match['name'])] = $match['quoted_string'] !== null ? preg_replace('/\A' . Semantics::DQUOTE . '|\x5C(.)|' . Semantics::DQUOTE . '\z/s', '$1', $match['quoted_string']) : $match['token'];
			}
		}

		if (
			$type === '' ||
			$subtype === '' ||
			$type === '*' ||
			$subtype === '*'
		) {
			return null;
		}

		return new MediaType($type, $subtype, $parameters);
	}
}
