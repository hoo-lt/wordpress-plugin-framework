<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\Bool;

use Closure;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\RuleInterface;

readonly class Rule implements RuleInterface
{
	public function break(mixed $value, Closure $closure): bool
	{
		$break = filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) === null;
		if ($break) {
			$closure('not a bool');
		}

		return $break;
	}
}
