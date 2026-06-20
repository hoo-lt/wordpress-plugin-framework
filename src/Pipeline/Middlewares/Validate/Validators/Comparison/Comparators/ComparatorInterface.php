<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Comparison\Comparators;

interface ComparatorInterface
{
	public function compare(mixed $a, mixed $b): ?int;
}
