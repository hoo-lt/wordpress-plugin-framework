<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Input;

use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Rules\RuleInterface;

interface InputInterface
{
	public function name(): string;
	public function value(): mixed;
	public function rules(): array;
	public function withRule(RuleInterface $rule): InputInterface;
}
