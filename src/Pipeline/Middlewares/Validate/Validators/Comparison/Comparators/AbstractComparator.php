<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison\Comparators;

use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison\Comparators\ComparatorInterface;

abstract readonly class AbstractComparator implements ComparatorInterface
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

		return $a <=> $b;
	}

	abstract protected function normalize(mixed $value): mixed;
}
