<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Rules\Enum;

use BackedEnum;
use Closure;
use Hoo\WordPressPluginFramework\{
	Pipeline\Middlewares\Validate\Rules\RuleInterface,
	Pipeline\Middlewares\Validate\Rules\RuleException,
};

readonly class Rule implements RuleInterface
{
	public function __construct(
		protected string $class,
	) {
		if (!is_subclass_of($this->class, BackedEnum::class)) {
			throw new RuleException("Class {$this->class} must be a BackedEnum.");
		}
	}

	public function __invoke(mixed $value, Closure $closure): void
	{
		if ($this->class::tryFrom($value) === null) {
			$closure('Invalid enum value');
		}
	}
}
