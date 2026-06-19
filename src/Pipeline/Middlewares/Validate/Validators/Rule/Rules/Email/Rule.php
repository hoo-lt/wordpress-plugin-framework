<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Rules\Email;

use Closure;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Rules\RuleInterface;

readonly class Rule implements RuleInterface
{
	public function __invoke(mixed $value, Closure $closure): void
	{
		if (filter_var($value, FILTER_VALIDATE_EMAIL, FILTER_NULL_ON_FAILURE) === null) {
			$closure('not an email');
		}
	}
}
