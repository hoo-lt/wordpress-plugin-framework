<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Rules\Int;

use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Rules\RuleInterface;

readonly class Rule implements RuleInterface
{
	public function __invoke(mixed $value): bool
	{
		return filter_var($value, FILTER_VALIDATE_INT) !== false;
	}

	public function error(): string
	{
		return 'must be an integer';
	}
}
