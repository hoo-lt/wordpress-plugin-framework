<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer;

use stdClass;

readonly class Normalizer implements NormalizerInterface
{
	public function __construct(
		protected array $normalizers,
	) {
	}

	public function normalizes(mixed $value): bool
	{
		return true;
	}

	public function normalize(mixed $value): mixed
	{
		foreach ($this->normalizers as $normalizer) {
			if ($normalizer->normalizes($value)) {
				return $normalizer->normalize($value);
			}
		}

		if (is_array($value)) {
			return $this->normalizeArray($value);
		}

		if (is_object($value)) {
			return $this->normalizeObject($value);
		}

		return $value;
	}

	protected function normalizeArray(array $array): array
	{
		$normalized = [];

		foreach ($array as $key => $value) {
			$normalized[$key] = $this->normalize($value);
		}

		return $normalized;
	}

	protected function normalizeObject(object $object): object
	{
		$normalized = new stdClass();

		foreach ($object as $key => $value) {
			$normalized->{$key} = $this->normalize($value);
		}

		return $normalized;
	}
}
