<?php

namespace Hoo\WordPressPluginFramework\Http\Request\Validator\Rules;

interface RuleInterface
{
	public function __invoke(mixed $value): void;
}
