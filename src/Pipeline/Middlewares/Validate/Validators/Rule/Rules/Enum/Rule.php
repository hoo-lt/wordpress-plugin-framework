<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\Enum;

use BackedEnum;
use Closure;
use Hoo\WordPressPluginFramework\{
	Pipeline\Middlewares\Validate\Validators\Rule\Rules\RuleInterface,
	Pipeline\Middlewares\Validate\Validators\Rule\Rules\RuleException,
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

	public function break(mixed $value, Closure $closure): bool
	{
		$break = !is_int($value) && !is_string($value) || $this->class::tryFrom($value) === null;
		if ($break) {
			$closure('Invalid enum value');
		}

		return $break;
	}
}
