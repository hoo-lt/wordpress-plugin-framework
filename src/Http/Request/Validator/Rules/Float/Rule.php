<?php

namespace Hoo\WordPressPluginFramework\Http\Request\Validator\Rules\Float;

use Hoo\WordPressPluginFramework\Http\Request\Validator\Rules\RuleException;
use Hoo\WordPressPluginFramework\Http\Request\Validator\Rules\RuleInterface;

readonly class Rule implements RuleInterface
{
	public function __invoke(mixed $value): void
	{
		if (filter_var($value, FILTER_VALIDATE_FLOAT) === false) {
			throw new RuleException('must be a number');
		}
	}
}
