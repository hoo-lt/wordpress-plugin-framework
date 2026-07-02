<?php

namespace Hoo\WordPressPluginFramework\Http\Negotiation;

readonly class MediaRange
{
	public function __construct(
		public string $type,
		public string $subtype,
		public float $q,
		public int $order,
	) {
	}

	public function specificity(): int
	{
		if ($this->type === '*') {
			return 0;
		}

		if ($this->subtype === '*') {
			return 1;
		}

		return 2;
	}

	public function accepts(string $mediaType): bool
	{
		[
			$type,
			$subtype,
		] = array_pad(explode('/', strtolower($mediaType), 2), 2, '*');

		if ($this->type === '*') {
			return true;
		}

		if ($this->type !== $type) {
			return false;
		}

		return $this->subtype === '*' || $this->subtype === $subtype;
	}
}
