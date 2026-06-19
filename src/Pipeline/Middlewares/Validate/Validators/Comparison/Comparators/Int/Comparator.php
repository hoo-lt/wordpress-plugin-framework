<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Comparators\Int;

use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Comparators\AbstractComparator;

readonly class Comparator extends AbstractComparator
{
	protected function normalize(mixed $value): ?int
	{
		return filter_var($value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
	}
}
