<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer\BackedEnum;

use BackedEnum;
use Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer\NormalizerInterface;

readonly class Normalizer implements NormalizerInterface
{
	public function normalizes(mixed $value): bool
	{
		return $value instanceof BackedEnum;
	}

	public function normalize(mixed $value): int|string
	{
		return $value->value;
	}
}
