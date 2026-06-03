<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Rules\Closure;

use Closure;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Rules\RuleInterface;

readonly class Rule implements RuleInterface
{
	public function __construct(
		protected Closure $closure,
	) {
	}

	public function __invoke(mixed $value, Closure $closure): void
	{
		if (($this->closure)($value) === false) {
			$closure('closure returned false');
		}
	}
}
