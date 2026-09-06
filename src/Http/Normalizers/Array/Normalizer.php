<?php

namespace Hoo\WordPressPluginFramework\Http\Normalizers\Array;

use Hoo\WordPressPluginFramework\{
	Http\Normalizers\NormalizerException,
	Http\Normalizers\NormalizerInterface,
	Http\Normalizers\NormalizersInterface,
};

readonly class Normalizer implements NormalizerInterface
{
	public function __construct(
		protected NormalizersInterface $normalizers,
	) {
	}

	public function normalize(mixed $unnormalized): array
	{
		if (!$this->normalizes($unnormalized)) {
			throw new NormalizerException('does not normalize');
		}

		$normalized = [];

		foreach ($unnormalized as $key => $value) {
			$normalizer = $this->normalizers->get($value);
			$normalized[$key] = $normalizer === null ? $value : $normalizer->normalize($value);
		}

		return $normalized;
	}

	public function normalizes(mixed $unnormalized): bool
	{
		if (!is_array($unnormalized)) {
			return false;
		}

		return true;
	}
}
