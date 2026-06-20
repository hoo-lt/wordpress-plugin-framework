<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\Ip;

use Closure;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\RuleInterface;

readonly class Rule implements RuleInterface
{
	public function break(mixed $value, Closure $closure): bool
	{
		if (filter_var($value, FILTER_VALIDATE_IP, FILTER_NULL_ON_FAILURE) === null) {
			$closure('not an ip');
		}

		return false;
	}
}
