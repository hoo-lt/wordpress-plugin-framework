<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Rules\String;

use Closure;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Rules\RuleInterface;

readonly class Rule implements RuleInterface
{
	public function __invoke(mixed $value, Closure $closure): void
	{
		if (!is_string($value)) {
			$closure('not a string');
		}
	}
}
