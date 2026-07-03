<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer\DateTime;

use DateTimeInterface;
use Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer\NormalizerInterface;

readonly class Normalizer implements NormalizerInterface
{
	public function normalizes(mixed $value): bool
	{
		return $value instanceof DateTimeInterface;
	}

	public function normalize(mixed $value): string
	{
		return $value->format(DateTimeInterface::ATOM);
	}
}
