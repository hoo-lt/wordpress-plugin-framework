<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer;

readonly class Normalizer implements NormalizerInterface
{
	private const MAX_DEPTH = 32;

	/**
	 * @param NormalizerInterface[] $normalizers
	 */
	public function __construct(
		private array $normalizers,
	) {
	}

	public function supports(mixed $value): bool
	{
		return is_array($value) || is_object($value);
	}

	public function normalize(mixed $value): array
	{
		return $this->recurse($value, 0);
	}

	private function recurse(mixed $value, int $depth): array
	{
		if ($depth >= self::MAX_DEPTH) {
			throw new NormalizerException('maximum normalization depth exceeded');
		}

		$array = is_array($value) ? $value : get_object_vars($value);

		return array_map(fn (mixed $item): mixed => $this->value($item, $depth), $array);
	}

	private function value(mixed $value, int $depth): mixed
	{
		foreach ($this->normalizers as $normalizer) {
			if ($normalizer->supports($value)) {
				return $normalizer->normalize($value);
			}
		}

		return match (true) {
			is_array($value), is_object($value) => $this->recurse($value, $depth + 1),
			default => $value,
		};
	}
}
