<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\Int;

use Closure;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\RuleInterface;

readonly class Rule implements RuleInterface
{
	public function break(mixed $value, Closure $closure): bool
	{
		$break = filter_var($value, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE) === null;
		if ($break) {
			$closure('not an int');
		}

		return $break;
	}
}
