<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer;

use stdClass;

readonly class Normalizer implements NormalizerInterface
{
	public function __construct(
		protected array $normalizers,
	) {
	}

	public function normalizes(mixed $unnormalized): bool
	{
		return true;
	}

	public function normalize(mixed $unnormalized): mixed
	{
		foreach ($this->normalizers as $normalizer) {
			if ($normalizer->normalizes($unnormalized)) {
				return $normalizer->normalize($unnormalized);
			}
		}

		if (is_array($unnormalized)) {
			return $this->normalizeArray($unnormalized);
		}

		if (is_object($unnormalized)) {
			return $this->normalizeObject($unnormalized);
		}

		return $unnormalized;
	}

	protected function normalizeArray(array $unnormalized): array
	{
		$normalized = [];

		foreach ($unnormalized as $key => $value) {
			$normalized[$key] = $this->normalize($value);
		}

		return $normalized;
	}

	protected function normalizeObject(object $unnormalized): object
	{
		$normalized = new stdClass();

		foreach ($unnormalized as $key => $value) {
			$normalized->{$key} = $this->normalize($value);
		}

		return $normalized;
	}
}
