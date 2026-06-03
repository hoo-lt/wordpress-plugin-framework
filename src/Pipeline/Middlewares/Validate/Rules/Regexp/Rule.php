<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Rules\Regexp;

use Closure;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Rules\RuleInterface;

readonly class Rule implements RuleInterface
{
	public function __construct(
		protected string $regexp,
	) {
	}

	public function __invoke(mixed $value, Closure $closure): void
	{
		if (
			filter_var($value, FILTER_VALIDATE_REGEXP, [
				'options' => [
					'regexp' => $this->regexp,
				],
				'flags' => FILTER_NULL_ON_FAILURE,
			]) === null
		) {
			$closure('not a regexp');
		}
	}
}
