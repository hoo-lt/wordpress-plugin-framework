<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Input;

use Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Rules\RuleInterface;

interface InputInterface
{
	public function name(): string;
	public function value(): mixed;
	public function rules(): array;
	public function withRule(RuleInterface $rule): InputInterface;
}
