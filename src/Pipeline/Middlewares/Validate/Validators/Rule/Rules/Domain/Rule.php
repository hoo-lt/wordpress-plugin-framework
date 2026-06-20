<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\Domain;

use Closure;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\RuleInterface;

readonly class Rule implements RuleInterface
{
	public function break(mixed $value, Closure $closure): bool
	{
		if (filter_var($value, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME | FILTER_NULL_ON_FAILURE) === null) {
			$closure('not a domain');
		}

		return false;
	}
}
