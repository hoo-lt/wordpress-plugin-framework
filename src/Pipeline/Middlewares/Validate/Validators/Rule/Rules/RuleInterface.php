<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Rules;

use Closure;

interface RuleInterface
{
	public function __invoke(mixed $value, Closure $closure): void;
}
