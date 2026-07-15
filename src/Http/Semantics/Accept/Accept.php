<?php

namespace Hoo\WordPressPluginFramework\Http\Semantics\Accept;

use Hoo\WordPressPluginFramework\{
	Http\Semantics\Accept\MediaRange\Precedence\Precedence,
	Http\Semantics\ContentType\MediaType\MediaTypeInterface,
};

readonly class Accept
{
	public function __construct(
		protected array $mediaRanges,
	) {
	}

	public function mediaRanges(): array
	{
		return $this->mediaRanges;
	}

	public function mediaTypes(): array
	{
		$mediaTypes = [];

		foreach ($this->mediaRanges as $mediaRange) {
			$mediaType = $mediaRange->mediaType();
			if ($mediaType === null) {
				continue;
			}

			$mediaTypes[] = $mediaType;
		}

		return $mediaTypes;
	}

	public function q(MediaTypeInterface $mediaType): ?float
	{
		foreach (Precedence::cases() as $precedence) {
			foreach ($this->mediaRanges as $mediaRange) {
				if ($precedence === $mediaRange->precedence($mediaType)) {
					return $mediaRange->q();
				}
			}
		}

		return null;
	}
}
