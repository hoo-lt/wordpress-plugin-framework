<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Rules\Float;

use Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Rules\RuleInterface;

readonly class Rule implements RuleInterface
{
	public function __invoke(mixed $value): bool
	{
		return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
	}

	public function error(): string
	{
		return 'must be a number';
	}
}
