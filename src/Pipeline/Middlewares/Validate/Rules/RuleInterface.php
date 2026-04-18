<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Rules;

interface RuleInterface
{
	public function __invoke(mixed $value): bool;
	public function error(): string;
}
