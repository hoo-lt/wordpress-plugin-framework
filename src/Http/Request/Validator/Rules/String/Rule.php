<?php

namespace Hoo\WordPressPluginFramework\Http\Request\Validator\Rules\String;

use Hoo\WordPressPluginFramework\Http\Request\Validator\Rules\RuleException;
use Hoo\WordPressPluginFramework\Http\Request\Validator\Rules\RuleInterface;

readonly class Rule implements RuleInterface
{
	public function __invoke(mixed $value): void
	{
		if (!is_string($value)) {
			throw new RuleException('must be a string');
		}
	}
}
