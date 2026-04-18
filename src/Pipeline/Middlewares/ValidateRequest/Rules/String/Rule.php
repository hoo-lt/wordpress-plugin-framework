<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Rules\String;

use Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Rules\RuleInterface;

readonly class Rule implements RuleInterface
{
	public function __invoke(mixed $value): bool
	{
		return is_string($value);
	}

	public function error(): string
	{
		return 'must be a string';
	}
}
