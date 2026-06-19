<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Comparators;

interface ComparatorInterface
{
	public function compare(mixed $a, mixed $b): ?int;
}
