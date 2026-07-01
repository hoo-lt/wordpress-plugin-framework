<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer\Object;

use Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer\{
	NormalizerInterface,
	TypeNormalizerInterface,
};

readonly class Normalizer implements TypeNormalizerInterface
{
	public function supports(mixed $value): bool
	{
		return is_object($value);
	}

	public function normalize(mixed $value, NormalizerInterface $normalizer): array
	{
		return array_map(
			fn (mixed $item): mixed => $normalizer->normalize($item),
			get_object_vars($value),
		);
	}
}
