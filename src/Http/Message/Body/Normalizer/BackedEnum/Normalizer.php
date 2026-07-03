<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer\BackedEnum;

use BackedEnum;
use Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer\NormalizerInterface;

readonly class Normalizer implements NormalizerInterface
{
	public function normalizes(mixed $unnormalized): bool
	{
		return $unnormalized instanceof BackedEnum;
	}

	public function normalize(mixed $unnormalized): int|string
	{
		return $unnormalized->value;
	}
}
