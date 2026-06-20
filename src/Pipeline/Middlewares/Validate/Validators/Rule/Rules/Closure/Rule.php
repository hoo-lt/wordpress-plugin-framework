<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\Closure;

use Closure;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\RuleInterface;

readonly class Rule implements RuleInterface
{
	public function __construct(
		protected Closure $closure,
	) {
	}

	public function break(mixed $value, Closure $closure): bool
	{
		if (($this->closure)($value) === false) {
			$closure('closure returned false');
		}

		return false;
	}
}
