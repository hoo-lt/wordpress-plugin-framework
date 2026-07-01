<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer;

readonly class Normalizer implements NormalizerInterface
{
	/**
	 * @var TypeNormalizerInterface[]
	 */
	private array $normalizers;

	public function __construct(
		TypeNormalizerInterface ...$normalizers,
	) {
		$this->normalizers = $normalizers;
	}

	public function normalize(mixed $value): mixed
	{
		foreach ($this->normalizers as $normalizer) {
			if ($normalizer->supports($value)) {
				return $normalizer->normalize($value, $this);
			}
		}

		return $value;
	}
}
