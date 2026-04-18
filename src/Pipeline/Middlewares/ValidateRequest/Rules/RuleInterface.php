<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\ValidateRequest\Rules;

interface RuleInterface
{
	public function __invoke(mixed $value): bool;
	public function error(): string;
}
