<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Input;

use Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Rules\RuleInterface;

interface InputInterface
{
	public function key(): string;
	public function value(): mixed;
	public function entries(): array;
	public function rules(): array;
	public function withRules(RuleInterface ...$rules): InputInterface;
}
