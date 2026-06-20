<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\Array;

use Closure;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\RuleInterface;

readonly class Rule implements RuleInterface
{
	public function break(mixed $value, Closure $closure): bool
	{
		$break = !is_array($value);
		if ($break) {
			$closure('not an array');
		}

		return $break;
	}
}
