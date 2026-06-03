<?php

namespace Hoo\WordPressPluginFramework\Pipeline\Middlewares\Validate\Validator;

use Closure;
use Hoo\WordPressPluginFramework\{
	Http\Request\RequestInterface,
	Pipeline\Middlewares\Validate\Rules\RuleInterface,
};

interface ValidatorInterface
{
	public function withRules(RuleInterface ...$rules): static;
	public function withoutRules(): static;

	public function withRule(RuleInterface $rule): static;

	public function validate(RequestInterface $request, Closure $closure): void;
}
