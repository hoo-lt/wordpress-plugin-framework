<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\Ip;

use Closure;
use Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validators\Rule\Rules\AbstractRule;

readonly class Rule extends AbstractRule
{
	public function break(mixed $value, Closure $closure): bool
	{
		parent::break($value, $closure);
		return false;
	}

	protected function normalize(mixed $value): ?string
	{
		return filter_var($value, FILTER_VALIDATE_IP, FILTER_NULL_ON_FAILURE);
	}

	protected function message(): string
	{
		return $this->translator->translate('Must be an ip');
	}
}
