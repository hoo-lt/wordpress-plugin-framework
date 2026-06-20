<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\Url;

use Closure;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\RuleInterface;

readonly class Rule implements RuleInterface
{
	public function break(mixed $value, Closure $closure): bool
	{
		if (filter_var($value, FILTER_VALIDATE_URL, FILTER_NULL_ON_FAILURE) === null) {
			$closure('not an url');
		}

		return false;
	}
}
