<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Comparators\String;

use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Comparators\ComparatorInterface;

readonly class Comparator implements ComparatorInterface
{
	public function compare(mixed $a, mixed $b): ?int
	{
		$a = $this->normalize($a);
		if ($a === null) {
			return null;
		}

		$b = $this->normalize($b);
		if ($b === null) {
			return null;
		}

		return strcmp($a, $b);
	}

	protected function normalize(mixed $value): ?string
	{
		if (
			!is_null($value) &&
			!is_scalar($value)
		) {
			return null;
		}

		return (string) $value;
	}
}
