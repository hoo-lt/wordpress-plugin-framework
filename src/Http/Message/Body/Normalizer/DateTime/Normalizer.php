<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer\DateTime;

use DateTimeInterface;
use Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer\NormalizerInterface;

readonly class Normalizer implements NormalizerInterface
{
	public function normalizes(mixed $unnormalized): bool
	{
		return $unnormalized instanceof DateTimeInterface;
	}

	public function normalize(mixed $unnormalized): string
	{
		return $unnormalized->format(DateTimeInterface::ATOM);
	}
}
