<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Comparators\Float;

use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Comparators\AbstractComparator;

readonly class Comparator extends AbstractComparator
{
	protected function normalize(mixed $value): ?float
	{
		return filter_var($value, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE);
	}
}
