<?php

namespace Hoo\WordPressPluginFramework\Http\Message\Body\Normalizer;

use BackedEnum;
use DateTimeInterface;

readonly class Normalizer implements NormalizerInterface
{
	public function normalize(array|object $body): array
	{
		$array = is_array($body) ? $body : get_object_vars($body);

		return array_map($this->value(...), $array);
	}

	private function value(mixed $value): mixed
	{
		return match (true) {
			$value instanceof BackedEnum => $this->enum($value),
			$value instanceof DateTimeInterface => $this->dateTime($value),
			is_array($value), is_object($value) => $this->normalize($value),
			default => $value,
		};
	}

	private function enum(BackedEnum $enum): int|string
	{
		return $enum->value;
	}

	private function dateTime(DateTimeInterface $dateTime): string
	{
		return $dateTime->format(DateTimeInterface::ATOM);
	}
}
