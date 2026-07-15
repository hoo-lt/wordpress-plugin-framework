<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\ContentType\MediaType;

use Hoo\WordPressPluginFramework\Http\Semantics\Semantics;

readonly class MediaTypeFactory implements MediaTypeFactoryInterface
{
	public function create(string $mediaType): MediaTypeInterface
	{
		preg_match_all('/\A' . Semantics::TYPE . '\/' . Semantics::SUBTYPE . '|' . Semantics::PARAMETERS . '/', $mediaType, $matches, PREG_SET_ORDER | PREG_UNMATCHED_AS_NULL);

		$parameters = [];
		foreach ($matches as $match) {
			if ($match['type'] !== null) {
				$type ??= strtolower($match['type']);
			}

			if ($match['subtype'] !== null) {
				$subtype ??= strtolower($match['subtype']);
			}

			if ($match['parameter'] !== null) {
				$parameters[strtolower($match['name'])] ??= $match['quoted_string'] !== null
					? $this->unquote($match['quoted_string'])
					: $match['token'];
			}
		}

		return new MediaType(
			$type ?? '',
			$subtype ?? '',
			$parameters,
		);
	}

	public function tryCreate(?string $mediaType): ?MediaTypeInterface
	{
		return $mediaType === null ? null : $this->create($mediaType);
	}

	private function unquote(string $quotedString): string
	{
		return preg_replace('/\A' . Semantics::DQUOTE . '|\x5C(.)|' . Semantics::DQUOTE . '\z/s', '$1', $quotedString);
	}
}
