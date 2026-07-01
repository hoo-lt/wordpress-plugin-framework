<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer\Enum;

use BackedEnum;
use Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer\{
	NormalizerInterface,
	TypeNormalizerInterface,
};

readonly class Normalizer implements TypeNormalizerInterface
{
	public function supports(mixed $value): bool
	{
		return $value instanceof BackedEnum;
	}

	public function normalize(mixed $value, NormalizerInterface $normalizer): int|string
	{
		return $value->value;
	}
}
