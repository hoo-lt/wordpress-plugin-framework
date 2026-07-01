<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer\DateTime;

use DateTimeInterface;
use Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer\{
	NormalizerInterface,
	TypeNormalizerInterface,
};

readonly class Normalizer implements TypeNormalizerInterface
{
	public function supports(mixed $value): bool
	{
		return $value instanceof DateTimeInterface;
	}

	public function normalize(mixed $value, NormalizerInterface $normalizer): string
	{
		return $value->format(DateTimeInterface::ATOM);
	}
}
